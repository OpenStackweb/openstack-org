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
class RemoteCloudsDirectoryPage extends MarketPlaceDirectoryPage
{
    static $allowed_children = "none";
}


class RemoteCloudsDirectoryPage_Controller extends MarketPlaceDirectoryPage_Controller
{

    private static $allowed_actions = array(
        'handleIndex','handleFilter'
    );

    static $url_handlers = array(
        'f/$Service/$Keyword' => 'handleFilter',
        '$Company!/$Slug!' => 'handleIndex',
    );

    /**
     * @var IOpenStackImplementationRepository
     */
    private $remote_cloud_repository;

    /**
     * @var RemoteCloudManager
     */
    private $remote_cloud_manager;

    /**
     * @var IQueryHandler
     */
    private $implementations_services_query;

    function init()
    {
        parent::init();

        Requirements::css(THIRDPARTY_DIR . '/jquery-ui-themes/smoothness/jquery-ui.css');
        Requirements::javascript(THIRDPARTY_DIR . '/jquery-ui/jquery-ui.js');

        Requirements::customScript("jQuery(document).ready(function($) {
            $('#remote-clouds','.marketplace-nav').addClass('current');
        });");
        Requirements::css("themes/openstack/css/chosen.css", "screen,projection");

        Requirements::combine_files('marketplace_remote_clouds_landing.js',
            array(
                "themes/openstack/javascript/chosen.jquery.min.js",
                "marketplace/code/ui/frontend/js/implementation.directory.page.js"
            ));

        Requirements::customScript($this->GATrackingCode());

        $this->remote_cloud_repository = new SapphireRemoteCloudRepository();

        $this->remote_cloud_manager = new RemoteCloudManager (
            $this->remote_cloud_repository,
            new SapphireMarketPlaceVideoTypeRepository,
            new SapphireMarketPlaceTypeRepository,
            new SapphireGuestOSTypeRepository,
            new SapphireHyperVisorTypeRepository,
            new SapphireOpenStackApiVersionRepository,
            new SapphireOpenStackComponentRepository,
            new SapphireOpenStackReleaseRepository,
            new SapphireRegionRepository,
            new SapphireSupportChannelTypeRepository,
            new SapphireOpenStackReleaseSupportedApiVersionRepository,
            new RemoteCloudAddPolicy($this->remote_cloud_repository, new SapphireMarketPlaceTypeRepository),
            new CompanyServiceCanAddResourcePolicy,
            new CompanyServiceCanAddVideoPolicy,
            new RemoteCloudFactory,
            new MarketplaceFactory,
            new ValidatorFactory,
            new OpenStackApiFactory,
            null,
            new SessionCacheService,
            SapphireTransactionManager::getInstance()
        );

        $this->implementations_services_query = new OpenStackImplementationServicesQueryHandler;
    }


    public function handleIndex()
    {
        $params = $this->request->allParams();
        if (isset($params["Company"]) && isset($params["Slug"])) {
            //render instance ...
            return $this->remote_cloud();
        }
    }

    public function handleFilter()
    {
        $keyword = $this->request->param('Keyword');
        $keyword_val = ($keyword == 'a') ? '' : $keyword;
        return $this->getViewer('')->process($this->customise(array('Keyword' => $keyword_val)));
    }


    public function getImplementations()
    {
        $list = $this->remote_cloud_manager->getActives();

        //return on view model
        return new ArrayList($list);
    }

    public function remote_cloud()
    {
        try {
            $params = $this->request->allParams();
            $company_url_segment = Convert::raw2sql($params["Company"]);
            $slug = Convert::raw2sql($params["Slug"]);
            $query = new QueryObject();
            $query->addAndCondition(QueryCriteria::equal('Slug', $slug));
            $remote_cloud = $this->remote_cloud_repository->getBy($query);
            if (!$remote_cloud || !$remote_cloud->Active) {
                throw new NotFoundEntityException('', '');
            }
            if ($remote_cloud->getCompany()->URLSegment != $company_url_segment) {
                throw new NotFoundEntityException('', '');
            }
            // we need this for reviews.
            $this->company_service_ID = $remote_cloud->getIdentifier();
            $render = new RemoteCloudSapphireRender($remote_cloud);

            return $render->draw();
        } catch (Exception $ex) {
            SS_Log::log($ex->getMessage(), SS_Log::WARN);
            return $this->httpError(404, 'Sorry that Cloud could not be found!.');
        }
    }

    public function ServicesCombo()
    {
        $service = $this->request->param('Service');
        $source = array();
        $result = $this->implementations_services_query->handle(new OpenStackImplementationNamesQuerySpecification(''));
        foreach ($result->getResult() as $dto) {
            $source[$dto->getValue()] = $dto->getValue();
        }

        $ddl = new DropdownField('service-term"', $title = null, $source, $service);
        $ddl->setEmptyString('-- Show All --');

        return $ddl;
    }
}