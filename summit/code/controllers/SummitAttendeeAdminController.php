<?php

/**
 * Copyright 2017 OpenStack Foundation
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
final class SummitAttendeeAdminController extends Controller
{

    /**
     * @var IEventbriteAttendeeRepository
     */
    private $eventbrite_attendee_repository;

    /**
     * @var  SummitAppAdminController The parent controller
     */
    protected $parent;

    private static $allowed_actions = array
    (
        'attendees',
        'editAttendee',
        'attendeesMatch',
    );

    private static $url_handlers = array
    (
        'match'                 => 'attendeesMatch',
        '$AttendeeID!'          => 'editAttendee',
        'GET '                  => 'attendees',
    );

    /**
     * SummitAttendeeAdminController constructor.
     * @param SummitAppAdminController $parent
     */
    public function __construct(SummitAppAdminController $parent)
    {
        parent::__construct();
        $this->parent       = $parent;
        $this->eventbrite_attendee_repository = new SapphireEventbriteAttendeeRepository();
    }

    public function Link($action = null)
    {
        return $this->parent->Link($action);
    }

    public function attendees(SS_HTTPRequest $request)
    {
        $summit_id = intval($request->param('SummitID'));

        $summit = Summit::get()->byID($summit_id);

        Requirements::customCSS("
        .bootstrap-tagsinput {
            width: 100% !important;
        }");
        Requirements::css('summit/css/simple-sidebar.css');
        JQueryValidateDependencies::renderRequirements(true, false);
        SweetAlert2Dependencies::renderRequirements();
        BootstrapTagsInputDependencies::renderRequirements();

        Requirements::javascript('summit/javascript/simple-sidebar.js');
        Requirements::javascript('themes/openstack/javascript/bootstrap-paginator/src/bootstrap-paginator.js');
        Requirements::javascript('themes/openstack/javascript/urlfragment.jquery.js');
        Requirements::javascript('themes/openstack/javascript/jquery-ajax-loader.js');
        Requirements::javascript('summit/javascript/summitapp-addattendee.js');

        return $this->parent->getViewer('attendees')->process
            (
                $this->customise
                    (
                        array
                        (
                            'Summit' => $summit,
                        )
                    )
            );
    }

    public function attendeesMatch(SS_HTTPRequest $request)
    {
        $summit_id = intval($request->param('SummitID'));
        $summit    = Summit::get()->byID($summit_id);

        Requirements::css('summit/css/simple-sidebar.css');
        Requirements::css('node_modules/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css');
        SweetAlert2Dependencies::renderRequirements();
        BootstrapTagsInputDependencies::renderRequirements();
        JQueryValidateDependencies::renderRequirements(true, false);
        Requirements::javascript('summit/javascript/simple-sidebar.js');
        Requirements::javascript('themes/openstack/javascript/bootstrap-paginator/src/bootstrap-paginator.js');
        Requirements::javascript('themes/openstack/javascript/urlfragment.jquery.js');
        Requirements::javascript('themes/openstack/javascript/jquery-ajax-loader.js');
        Requirements::javascript('themes/openstack/javascript/jquery.cleanform.js');
        Requirements::javascript('summit/javascript/summit-admin-attendees-match.js');

        list($orphan_attendees, $count) = $this->eventbrite_attendee_repository->getUnmatchedPaged('', false, 1, 20, $summit_id);

        return $this->parent->getViewer('attendees_match')->process
            (
                $this->customise
                    (
                        [
                            'Summit'         => $summit,
                            'Attendees'      => $orphan_attendees,
                            'TotalAttendees' => $count
                        ]
                    )
            );
    }

    public function editAttendee(SS_HTTPRequest $request)
    {
        $summit_id = intval($request->param('SummitID'));
        $summit = Summit::get()->byID($summit_id);
        $attendee_id = intval($request->param('AttendeeID'));
        $attendee = SummitAttendee::get()->byID($attendee_id);

        Requirements::css('summit/css/simple-sidebar.css');
        Requirements::css('node_modules/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css');
        SweetAlert2Dependencies::renderRequirements();
        JQueryValidateDependencies::renderRequirements(true, false);
        JSChosenDependencies::renderRequirements();
        Requirements::javascript('node_modules/bootstrap-3-typeahead/bootstrap3-typeahead.min.js');
        Requirements::javascript('summit/javascript/simple-sidebar.js');
        TinyMceDependencies::renderRequirements();
        Requirements::javascript('summit/javascript/summitapp-editattendee.js');

        return $this->parent->getViewer('EditAttendee')->process
            (
                $this->customise
                    (
                        [
                            'Summit'   => $summit,
                            'Attendee' => $attendee,
                        ]
                    )
            );
    }

}