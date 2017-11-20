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
final class SummitAppAdminController extends Controller implements PermissionProvider
{

    /**
     * Return a map of permission codes to add to the dropdown shown in the Security section of the CMS.
     * array(
     *   'VIEW_SITE' => 'View the site',
     * );
     */
    public function providePermissions()
    {

        return array(
            'ADMIN_SUMMIT_APP_FRONTEND_ADMIN' => array(
                'name'     => 'Full Access to Summit FrontEnd Admin',
                'category' => 'Summit Application',
                'help'     => '',
                'sort'     => 2
            ),
        );
    }

    public function init()
    {
        parent::init();

        if(!Member::currentUser())
            return OpenStackIdCommon::doLogin();

        if(!Permission::check("ADMIN_SUMMIT_APP_FRONTEND_ADMIN")) Security::permissionFailure($this);
        JQueryCoreDependencies::renderRequirements();
        BootstrapDependencies::renderRequirements();
        FontAwesomeDependencies::renderRequirements();
        JQueryValidateDependencies::renderRequirements(true, false);
        JSChosenDependencies::renderRequirements();

        Requirements::css('//fonts.googleapis.com/css?family=Open+Sans:300,400,700');
        Requirements::css("themes/openstack/css/combined.css");
        Requirements::css("themes/openstack/css/navigation_menu.css");
        Requirements::css("themes/openstack/css/dropdown.css");
        BootstrapTagsInputDependencies::renderRequirements();
        Requirements::css("node_modules/jquery-datetimepicker/build/jquery.datetimepicker.min.css");
        Requirements::css('summit/css/summit-admin.css');

        Requirements::javascript('node_modules/moment/min/moment.min.js');
        Requirements::javascript("node_modules/jquery-datetimepicker/build/jquery.datetimepicker.full.min.js");
        Requirements::javascript('themes/openstack/javascript/urlfragment.jquery.js');
        JQueryUIDependencies::renderRequirements(JQueryUIDependencies::SmoothnessTheme);

        Requirements::javascript('summit/javascript/bootstrap-dropdown.js');

        Requirements::javascript('themes/openstack/javascript/jquery.serialize.js');
    }

    private static $url_segment = 'summit-admin';

    private static $allowed_actions = array
    (
        'directory',
        'dashboard',
        'reports',
        'ticketTypes',
        'handleEvents',
        'handleAttendees',
        'handleSpeakers',
        'handlePromocodes',
    );

    private static $url_handlers = array
    (
        '$SummitID!/dashboard'                                       => 'dashboard',
        '$SummitID!/reports/$Report'                                 => 'reports',
        '$SummitID!/tickets'                                         => 'ticketTypes',
        '$SummitID!/events'                                          => 'handleEvents',
        '$SummitID!/attendees'                                       => 'handleAttendees',
        '$SummitID!/speakers'                                        => 'handleSpeakers',
        '$SummitID!/promocodes'                                      => 'handlePromocodes',
    );

    /**
     * Ensure all root requests go to login
     * @return SS_HTTPResponse
     */
    public function index()
    {
        return $this->redirect($this->Link('directory'));
    }

    public function Link($action = null)
    {
        return Controller::join_links($this->config()->url_segment, $action);
    }

    public function handleEvents(SS_HTTPRequest $r) {
        return new SummitEventAdminController($this);
    }

    public function handleAttendees(SS_HTTPRequest $r) {
        return new SummitAttendeeAdminController($this);
    }

    public function handleSpeakers(SS_HTTPRequest $r) {
        return new PresentationSpeakerAdminController($this);
    }

    public function handlePromocodes(SS_HTTPRequest $r) {
        return new SummitPromocodesAdminController($this);
    }

    public function directory()
    {
        $summits = Summit::get();
        return $this->getViewer('directory')->process
        (
          $this->customise
          (
              array
              (
                  'Summits' => $summits
              )
          )
        );
    }

    public function dashboard(SS_HTTPRequest $request)
    {
        $summit_id = intval($request->param('SummitID'));
        $summit = Summit::get()->byID($summit_id);

        $vote_repository = new SapphirePresentationVoteRepository();

        $votes = $vote_repository->getVoteCountBySummit($summit_id);
        $voters = $vote_repository->getVotersCountBySummit($summit_id);

        Requirements::css('summit/css/simple-sidebar.css');
        Requirements::javascript('summit/javascript/simple-sidebar.js');
        return $this->getViewer('dashboard')->process
        (
            $this->customise
            (
                array
                (
                    'Summit' => $summit,
                    'Votes'  => $votes,
                    'Voters'  => $voters
                )
            )
        );
    }

    public function ticketTypes(SS_HTTPRequest $request)
    {
        $summit_id = intval($request->param('SummitID'));

        $summit = Summit::get()->byID($summit_id);

        Requirements::css('summit/css/simple-sidebar.css');
        Requirements::javascript('summit/javascript/simple-sidebar.js');
        return $this->getViewer('ticketTypes')->process
        (
            $this->customise
            (
                array
                (
                    'Summit' => $summit
                )
            )
        );
    }

    public function reports(SS_HTTPRequest $request)
    {
        $report = $request->param('Report');
        $summit_id = intval($request->param('SummitID'));
        $summit = Summit::get()->byID($summit_id);

        Requirements::css('summit/css/simple-sidebar.css');
        Requirements::css('summit/css/summit-admin-reports.css');
        SweetAlert2Dependencies::renderRequirements();
        Requirements::css('node_modules/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css');
        Requirements::javascript('summit/javascript/simple-sidebar.js');
        Requirements::javascript('themes/openstack/javascript/bootstrap-paginator/src/bootstrap-paginator.js');
        Requirements::javascript('themes/openstack/javascript/jquery-ajax-loader.js');
        Requirements::javascript('summit/javascript/jquery.tabletoCSV.js');
        Requirements::javascript('//tinymce.cachefly.net/4.3/tinymce.min.js');

        //JS libraries for feedback form and list
        Requirements::javascript('marketplace/code/ui/frontend/js/star-rating.min.js');
        Requirements::css("marketplace/code/ui/frontend/css/star-rating.min.css");

        return $this->getViewer('reports')->process
            (
                $this->customise
                    (
                        array
                        (
                            'Summit' => $summit,
                            'Report' => $report,
                            'ReportName' => ucwords(implode(' ',explode('_',$report)))
                        )
                    )
            );
    }

    public function Time(){
        return time();
    }

}