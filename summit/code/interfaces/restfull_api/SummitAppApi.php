<?php
/**
 * Copyright 2015 OpenStack Foundation
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
 * Class SummitAppApi
 */
final class SummitAppApi extends AbstractRestfulJsonApi
{

    /**
     *
     */
    const ApiPrefix = 'api/v1/summitapp';

    /**
     * @var IEntityRepository
     */
    private $summit_repository;

    /**
     * @var ISummitManager
     */
    private $summit_manager;

    /**
     * SummitAppApi constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->summit_repository     = new SapphireSummitRepository;
        $this->summit_manager        = new SummitManager
        (
            $this->summit_repository,
            new SummitFactory(),
            SapphireTransactionManager::getInstance()
        );

        $this_var = $this;

        $this->addBeforeFilter('createSummit', 'check_create', function ($request) use ($this_var) {
            if (!$this_var->checkAdminPermissions($request)) {
                return $this_var->permissionFailure();
            }
        });

    }

    /**
     * @return bool
     */
    public function checkOwnAjaxRequest()
    {
        $referer = @$_SERVER['HTTP_REFERER'];
        if (empty($referer)) {
            return false;
        }
        //validate
        if (!Director::is_ajax()) {
            return false;
        }
        return Director::is_site_url($referer);
    }

    /**
     * @param $request
     * @return bool
     */
    public function checkAdminPermissions($request)
    {
        return true; //Permission::check("SUMMITAPP_ADMIN_ACCESS");
    }

    /**
     * @return bool
     */
    protected function isApiCall()
    {
        $request = $this->getRequest();
        if (is_null($request)) {
            return false;
        }
        return strpos(strtolower($request->getURL()), self::ApiPrefix) !== false;
    }

    /**
     * @return bool
     */
    protected function authorize()
    {
        return true;
    }

    /**
     * @return bool
     */
    protected function authenticate()
    {
        return true;
    }

    /**
     * @var array
     */
    static $url_handlers = array(
        'PUT new-summit' => 'createSummit',
        'PUT $SummitID!/delete' => 'deleteSummit',
    );

    /**
     * @var array
     */
    static $allowed_actions = array(
        'createSummit',
        'deleteSummit',
    );

    /**
     * @return SS_HTTPResponse
     */
    public function createSummit()
    {
        try {
            $data = $this->getJsonRequest();
            if (!$data) {
                return $this->serverError();
            }

            $summit = $this->summit_manager->createSummit($data);

            echo $this->buildSummitTableRow($summit);
        } catch (EntityAlreadyExistsException $ex1) {
            SS_Log::log($ex1, SS_Log::ERR);
            return $this->addingDuplicate($ex1->getMessage());
        }
    }

    /**
     * @param $summit
     * @return string
     */
    public function buildSummitTableRow($summit)
    {
        $start_date = ($summit->getBeginDate()) ? date('M jS Y', strtotime($summit->getBeginDate())) : '';
        $end_date = ($summit->getEndDate()) ? date('M jS Y', strtotime($summit->getEndDate())) : '';
        $summit_id = $summit->getIdentifier();

        $html = ' <tr id="summit_' . $summit_id . '">
                        <td class="summit_name">
                            ' . $summit->getName() . '
                        </td>
                        <td>
                            ' . $start_date . '
                        </td>
                        <td>
                            ' . $end_date . '
                        </td>
                        <td class="center_text">
                            <a href="summit-admin/' . $summit_id . '/dashboard" class="btn btn-primary btn-sm" role="button">Control Panel</a>
                        </td>
                        <td class="center_text">
                            <a href="$Top.Link/' . $summit_id . '/edit" class="btn btn-default btn-sm" role="button">Edit</a>
                            <a href="#delete_summit_modal" data-toggle="modal" data-summit-id="' . $summit_id . '" class="btn btn-danger btn-sm delete_summit">Delete</a>
                        </td>
                    </tr>';

        return $html;
    }

    /**
     * @return SS_HTTPResponse
     */
    public function deleteSummit()
    {
        try {
            $summit_id = (int)$this->request->param('SummitID');
            $this->summit_manager->deleteSummit($summit_id);
            return $this->updated();
        } catch (NotFoundEntityException $ex1) {
            SS_Log::log($ex1, SS_Log::WARN);
            return $this->notFound($ex1->getMessage());
        } catch (Exception $ex) {
            SS_Log::log($ex, SS_Log::ERR);
            return $this->serverError();
        }
    }

}