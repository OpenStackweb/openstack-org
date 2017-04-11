<?php
/**
 * Copyright 2014 Openstack Foundation
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 **/

/**
 * Class RemoteCloud_Controller
 */
class RemoteCloud_Controller extends AbstractController
{

    /**
     * @var IOpenStackImplementationNamesQueryHandler
     */
    private $implementations_names_query;
    /**
     * @var ICompanyServiceRepository
     */
    private $remote_cloud_repository;

    private $implementations_services_query;
    /**
     * @var array
     */
    static $url_handlers = array(
        'POST search' => 'search',
        'GET names' => 'names',
    );

    /**
     * @var array
     */
    static $allowed_actions = array(
        'search',
        'names',
    );

    function init()
    {
        parent::init();
        $this->implementations_names_query = new OpenStackImplementationNamesQueryHandler;
        $this->remote_cloud_repository = new SapphireRemoteCloudRepository();
    }

    function names()
    {
        $params = $this->request->getVars();
        $result = $this->implementations_names_query->handle(new OpenStackImplementationNamesQuerySpecification($params["term"]));
        $res = array();
        foreach ($result->getResult() as $dto) {
            array_push($res, array('label' => $dto->getLabel(), 'value' => $dto->getValue()));
        }
        return json_encode($res);
    }

    /**
     * @param string $action
     * @return string
     */
    public function Link($action = null)
    {
        $page = RemoteCloudsDirectoryPage::get()->first();
        if (is_null($page)) return '';
        $controller = ModelAsController::controller_for($page);
        if (is_null($controller)) return '';
        return $controller->Link($action);
    }

    function search()
    {
        $output = '';
        if (!$this->isJson()) {
            return $this->httpError(500, 'Content Type not allowed');
        }
        try {
            $search_params = json_decode($this->request->getBody(), true);
            $query1 = new QueryObject(new RemoteCloudService());
            $query1->addAlias(QueryAlias::create('Company'));
            $name = @$search_params['name_term'];
            $service = @$search_params['service_term'];

            if (!empty($name)) {
                $query1->addAndCondition(
                    QueryCompoundCriteria::compoundOr(array(
                            QueryCriteria::like('CompanyService.Name', $name),
                            QueryCriteria::like('CompanyService.Overview', $name),
                            QueryCriteria::like('Company.Name', $name)
                        )
                    )
                );
            }

            if (!empty($service)) {
                $service = explode('-', $service);
                $query1->addAlias
                (
                    QueryAlias::create('Capabilities')
                    ->addAlias
                    (
                        QueryAlias::create('ReleaseSupportedApiVersion')
                       ->addAlias
                       (
                           QueryAlias::create('OpenStackComponent')
                       )
                    )
                );
                $query1->addAndCondition(QueryCompoundCriteria::compoundOr( array (
                    QueryCriteria::like('OpenStackComponent.Name', trim($service[0])),
                    QueryCriteria::like('OpenStackComponent.CodeName', trim($service[1])
                    )
                )));
            }

            $query1->addAndCondition(QueryCriteria::equal("Active", true));

            list($implementations, $size1) = $this->remote_cloud_repository->getAll($query1, 0, 1000);

            foreach ($implementations as $implementation) {
                $type    = $implementation->getMarketPlace()->getName() == 'remote_cloud';
                $output .= $implementation->renderWith('RemoteCloudsDirectoryPage_ImplementationBox', array(
                    'Link'     => $this->Link("remote_cloud")));
            }
        } catch (Exception $ex) {
            return $this->httpError(500, 'Server Error');
        }
        return empty($output) ? $this->httpError(404, '') : $output;
    }
} 