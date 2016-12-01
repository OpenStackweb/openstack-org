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
     * @var ISummitEventRepository
     */
    private $event_repository;
    /**
     * @var ISummitRegistrationPromoCodeRepository
     */
    private $promocode_repository;
    /**
     * @var IEventbriteAttendeeRepository
     */
    private $eventbrite_attendee_repository;

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

        Requirements::css("themes/openstack/bower_assets/bootstrap/dist/css/bootstrap.min.css");
        Requirements::css("themes/openstack/bower_assets/fontawesome/css/font-awesome.min.css");
        Requirements::css('//fonts.googleapis.com/css?family=Open+Sans:300,400,700');
        Requirements::css("themes/openstack/css/combined.css");
        Requirements::css("themes/openstack/css/navigation_menu.css");
        Requirements::css("themes/openstack/css/dropdown.css");
        Requirements::css('themes/openstack/css/chosen.css');
        Requirements::css('summit/bower_components/bootstrap-tagsinput/dist/bootstrap-tagsinput.css');
        Requirements::css("themes/openstack/javascript/datetimepicker/jquery.datetimepicker.css");
        Requirements::css('summit/css/summit-admin.css');

        Requirements::javascript("themes/openstack/bower_assets/jquery/dist/jquery.min.js");
        Requirements::javascript("themes/openstack/bower_assets/jquery-migrate/jquery-migrate.min.js");

        Requirements::javascript("themes/openstack/bower_assets/bootstrap/dist/js/bootstrap.min.js");
        Requirements::javascript('themes/openstack/javascript/chosen.jquery.min.js');
        Requirements::javascript('themes/openstack/bower_assets/moment/min/moment.min.js');
        Requirements::javascript("themes/openstack/javascript/datetimepicker/jquery.datetimepicker.js");
        Requirements::javascript('themes/openstack/javascript/urlfragment.jquery.js');

        Requirements::javascript("themes/openstack/bower_assets/jquery-ui/jquery-ui.min.js");
        Requirements::javascript("themes/openstack/javascript/jquery-ui-bridge.js");
        if (Director::isLive())
        {
            Requirements::javascript("themes/openstack/bower_assets/jquery-validate/dist/jquery.validate.min.js");
            Requirements::javascript("themes/openstack/bower_assets/jquery-validate/dist/additional-methods.min.js");
        }
        else
        {
            Requirements::javascript("themes/openstack/bower_assets/jquery-validate/dist/jquery.validate.js");
            Requirements::javascript("themes/openstack/bower_assets/jquery-validate/dist/additional-methods.js");
        }
        Requirements::javascript('summit/javascript/bootstrap-dropdown.js');
        Requirements::javascript('summit/bower_components/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js');
        Requirements::javascript('themes/openstack/javascript/jquery.serialize.js');

        $this->event_repository = new SapphireSummitEventRepository();
        $this->promocode_repository = new SapphireSummitRegistrationPromoCodeRepository();
        $this->eventbrite_attendee_repository = new SapphireEventbriteAttendeeRepository();
    }

    private static $url_segment = 'summit-admin';

    private static $allowed_actions = array
    (
        'directory',
        'dashboard',
        'reports',
        'publishedEvents',
        'pendingEvents',
        'editEvent',
        'presentationLists',
        'editPresentationList',
        'ticketTypes',
        'attendees',
        'attendees_match',
        'editAttendee',
        'editSummit',
        'scheduleView',
        'scheduleViewEditBulkAction',
        'speakers',
        'editSpeaker',
        'promocodes',
        'editPromoCode',
        'promocodes_sponsors',
        'editPromoCodeSponsor',
        'promocodes_bulk',
        'speakers_merge',
        'events_bulk',
    );

    private static $url_handlers = array
    (
        '$SummitID!/dashboard'                                       => 'dashboard',
        '$SummitID!/reports/$Report'                                 => 'reports',
        '$SummitID!/events/published'                                => 'publishedEvents',
        '$SummitID!/events/bulk-action'                              => 'scheduleViewEditBulkAction',
        '$SummitID!/events/schedule'                                 => 'scheduleView',
        '$SummitID!/events/unpublished'                              => 'pendingEvents',
        '$SummitID!/events/presentation-lists/$PresentationListId!'  => 'editPresentationList',
        '$SummitID!/events/presentation-lists'                       => 'presentationLists',
        '$SummitID!/events/bulk'                                     => 'events_bulk',
        '$SummitID!/events/$EventID'                                 => 'editEvent',
        '$SummitID!/tickets'                                         => 'ticketTypes',
        '$SummitID!/attendees/match'                                 => 'attendees_match',
        '$SummitID!/attendees/$AttendeeID!'                          => 'editAttendee',
        '$SummitID!/attendees'                                       => 'attendees',
        '$SummitID!/edit'                                            => 'editSummit',
        '$SummitID!/speakers/merge'                                  => 'speakers_merge',
        '$SummitID!/speakers/$SpeakerID!'                            => 'editSpeaker',
        '$SummitID!/speakers'                                        => 'speakers',
        '$SummitID!/promocodes/sponsors/$SponsorID!'                 => 'editPromoCodeSponsor',
        '$SummitID!/promocodes/sponsors'                             => 'promocodes_sponsors',
        '$SummitID!/promocodes/bulk'                                 => 'promocodes_bulk',
        '$SummitID!/promocodes/$Code!'                               => 'editPromoCode',
        '$SummitID!/promocodes'                                      => 'promocodes',
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

    public function publishedEvents(SS_HTTPRequest $request)
    {
        $summit_id = intval($request->param('SummitID'));

        $summit = Summit::get()->byID($summit_id);

        Requirements::css('summit/css/simple-sidebar.css');
        Requirements::javascript('summit/javascript/simple-sidebar.js');

        $events = $summit->Events()->filter('Published', true);

        return $this->getViewer('publishedEvents')->process
        (
            $this->customise
            (
                array
                (
                    'Summit' => $summit,
                    'Events' => $events
                )
            )
        );
    }

    public function pendingEvents(SS_HTTPRequest $request)
    {
        $summit_id = intval($request->param('SummitID'));

        $summit = Summit::get()->byID($summit_id);

        Requirements::css('summit/css/simple-sidebar.css');
        Requirements::javascript('summit/javascript/simple-sidebar.js');

        $events = $summit->Events()->filter('Published', false);

        Requirements::javascript('summit/javascript/pending-events.js');

        return $this->getViewer('pendingEvents')->process
        (
            $this->customise
            (
                array
                (
                    'Summit' => $summit,
                    'Events' => $events
                )
            )
        );
    }

    public function presentationLists(SS_HTTPRequest $request)
    {
        $summit_id = intval($request->param('SummitID'));

        $summit = Summit::get()->byID($summit_id);

        Requirements::css('summit/css/simple-sidebar.css');
        Requirements::javascript('summit/javascript/simple-sidebar.js');

        $events = $summit->Events()->filter('Published', false);

        return $this->getViewer('presentationLists')->process
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

    public function editPresentationList(SS_HTTPRequest $request)
    {
        $summit_id = intval($request->param('SummitID'));

        $summit = Summit::get()->byID($summit_id);

        Requirements::css('summit/css/simple-sidebar.css');
        Requirements::javascript('summit/javascript/simple-sidebar.js');


        return $this->getViewer('editPresentationList')->process
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

    public function dashboard(SS_HTTPRequest $request)
    {
        $summit_id = intval($request->param('SummitID'));

        $summit = Summit::get()->byID($summit_id);

        Requirements::css('summit/css/simple-sidebar.css');
        Requirements::javascript('summit/javascript/simple-sidebar.js');
        return $this->getViewer('dashboard')->process
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

    public function attendees(SS_HTTPRequest $request)
    {
        $summit_id = intval($request->param('SummitID'));

        $summit = Summit::get()->byID($summit_id);

        Requirements::customCSS("
        .bootstrap-tagsinput {
            width: 100% !important;
        }");
        Requirements::css('summit/css/simple-sidebar.css');
        Requirements::css('themes/openstack/bower_assets/bootstrap-tagsinput/dist/bootstrap-tagsinput.css');
        Requirements::css('themes/openstack/bower_assets/bootstrap-tagsinput/dist/bootstrap-tagsinput-typeahead.css');
        Requirements::css('themes/openstack/bower_assets/sweetalert/dist/sweetalert.css');
        Requirements::javascript('themes/openstack/bower_assets/sweetalert/dist/sweetalert.min.js');
        Requirements::javascript('themes/openstack/bower_assets/jquery-validate/dist/jquery.validate.min.js');
        Requirements::javascript('themes/openstack/bower_assets/jquery-validate/dist/additional-methods.min.js');
        Requirements::javascript('themes/openstack/bower_assets/typeahead.js/dist/typeahead.bundle.min.js');
        Requirements::javascript('themes/openstack/bower_assets/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js');
        Requirements::javascript('summit/javascript/simple-sidebar.js');
        Requirements::javascript('themes/openstack/javascript/bootstrap-paginator/src/bootstrap-paginator.js');
        Requirements::javascript('themes/openstack/javascript/urlfragment.jquery.js');
        Requirements::javascript('themes/openstack/javascript/jquery-ajax-loader.js');
        Requirements::javascript('summit/javascript/summitapp-addattendee.js');


        return $this->getViewer('attendees')->process
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

    public function attendees_match(SS_HTTPRequest $request)
    {
        $summit_id = intval($request->param('SummitID'));
        $summit = Summit::get()->byID($summit_id);

        Requirements::css('summit/css/simple-sidebar.css');
        // tag inputes
        Requirements::css('themes/openstack/bower_assets/bootstrap-tagsinput/dist/bootstrap-tagsinput.css');
        Requirements::css('themes/openstack/bower_assets/bootstrap-tagsinput/dist/bootstrap-tagsinput-typeahead.css');
        Requirements::css('themes/openstack/bower_assets/sweetalert/dist/sweetalert.css');
        Requirements::css('themes/openstack/bower_assets/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css');
        //Requirements::css('summit/css/summit-admin-speaker-merge.css');

        Requirements::javascript('summit/javascript/simple-sidebar.js');
        Requirements::javascript('themes/openstack/javascript/bootstrap-paginator/src/bootstrap-paginator.js');
        Requirements::javascript('themes/openstack/javascript/urlfragment.jquery.js');
        Requirements::javascript('themes/openstack/javascript/jquery-ajax-loader.js');
        Requirements::javascript('themes/openstack/bower_assets/sweetalert/dist/sweetalert.min.js');
        Requirements::javascript('themes/openstack/bower_assets/jquery-validate/dist/jquery.validate.min.js');
        Requirements::javascript('themes/openstack/bower_assets/jquery-validate/dist/additional-methods.min.js');
        Requirements::javascript('themes/openstack/bower_assets/typeahead.js/dist/typeahead.bundle.min.js');
        Requirements::javascript('themes/openstack/bower_assets/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js');
        Requirements::javascript('themes/openstack/javascript/jquery.cleanform.js');
        Requirements::javascript('summit/javascript/summit-admin-attendees-match.js');

        list($orphan_attendees, $count) = $this->eventbrite_attendee_repository->getUnmatchedPaged();

        return $this->getViewer('attendees_match')->process
            (
                $this->customise
                    (
                        array
                        (
                            'Summit' => $summit,
                            'Attendees' => $orphan_attendees,
                            'TotalAttendees' => $count
                        )
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
        Requirements::css('themes/openstack/bower_assets/chosen/chosen.min.css');
        Requirements::css('themes/openstack/bower_assets/sweetalert/dist/sweetalert.css');
        Requirements::css('themes/openstack/bower_assets/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css');
        Requirements::javascript('themes/openstack/bower_assets/sweetalert/dist/sweetalert.min.js');
        Requirements::javascript('themes/openstack/bower_assets/jquery-validate/dist/jquery.validate.min.js');
        Requirements::javascript('themes/openstack/bower_assets/jquery-validate/dist/additional-methods.min.js');
        Requirements::javascript('themes/openstack/bower_assets/chosen/chosen.jquery.min.js');
        Requirements::javascript('themes/openstack/bower_assets/bootstrap3-typeahead/bootstrap3-typeahead.min.js');
        Requirements::javascript('summit/javascript/simple-sidebar.js');
        Requirements::javascript('//tinymce.cachefly.net/4.3/tinymce.min.js');
        Requirements::javascript('summit/javascript/summitapp-editattendee.js');

        return $this->getViewer('EditAttendee')->process
            (
                $this->customise
                    (
                        array
                        (
                            'Summit'   => $summit,
                            'Attendee' => $attendee,
                        )
                    )
            );
    }

    public function editEvent(SS_HTTPRequest $request)
    {
        $summit_id = intval($request->param('SummitID'));
        $summit = Summit::get()->byID($summit_id);
        $event_id = intval($request->param('EventID'));
        $event = ($event_id == 0) ? null : SummitEvent::get()->byID($event_id);

        Requirements::css('summit/css/simple-sidebar.css');
        Requirements::css('themes/openstack/bower_assets/chosen/chosen.min.css');
        Requirements::css('themes/openstack/bower_assets/sweetalert/dist/sweetalert.css');
        Requirements::css('themes/openstack/bower_assets/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css');
        Requirements::css('summit/css/summit-admin-edit-event.css');
        // tag inputes
        Requirements::css('themes/openstack/bower_assets/bootstrap-tagsinput/dist/bootstrap-tagsinput.css');
        Requirements::css('themes/openstack/bower_assets/bootstrap-tagsinput/dist/bootstrap-tagsinput-typeahead.css');

        Requirements::javascript('themes/openstack/bower_assets/sweetalert/dist/sweetalert.min.js');
        Requirements::javascript('themes/openstack/bower_assets/jquery-validate/dist/jquery.validate.min.js');
        Requirements::javascript('themes/openstack/bower_assets/jquery-validate/dist/additional-methods.min.js');
        Requirements::javascript('themes/openstack/bower_assets/chosen/chosen.jquery.min.js');
        Requirements::javascript('summit/javascript/simple-sidebar.js');
        Requirements::javascript('//tinymce.cachefly.net/4.3/tinymce.min.js');
        //tags inputs
        Requirements::javascript('themes/openstack/bower_assets/typeahead.js/dist/typeahead.bundle.min.js');
        Requirements::javascript('themes/openstack/bower_assets/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js');
        Requirements::javascript('summit/javascript/summitapp-editevent.js');

        return $this->getViewer('EditEvent')->process
        (
            $this->customise
            (
                array
                (
                    'Summit'   => $summit,
                    'Event'    => $event,
                    'Tab'      => (($event) ? 'schedule' : 'edit_event'),
                )
            )
        );
    }

    public function editSummit(SS_HTTPRequest $request)
    {
        Requirements::javascript('summit/javascript/summitapp-summitform.js');
        Requirements::css('summit/css/summit-admin.css');

        return $this->getViewer('EditSummit')->process($this->owner);
    }

    public function summitForm()
    {
        $summit_id = intval($this->request->param('SummitID'));
        $summit = Summit::get()->byID($summit_id);

        $form = SummitForm::create($summit,$this, "SummitForm", FieldList::create(FormAction::create('saveSummit','Save')));
        if($data = Session::get("FormInfo.{$form->FormName()}.data")) {
            $form->loadDataFrom($data);
        }
        else {
            $form->loadDataFrom($summit);
        }

        return $form;
    }

    public function saveSummit($data, $form) {
        Session::set("FormInfo.{$form->FormName()}.data", $data);
        /*if(empty(strip_tags($data['Bio']))) {
            $form->addErrorMessage('Bio','Please enter a bio', 'bad');
            return $this->redirectBack();
        }

        $speaker = Member::currentUser()->getCurrentSpeakerProfile();
        $form->saveInto($speaker);
        $speaker->write();

        $form->sessionMessage('Your bio has been updated', 'good');

        Session::clear("FormInfo.{$form->FormName()}.data", $data);*/

        return $this->redirectBack();
    }

    public function scheduleView(SS_HTTPRequest $request)
    {
        Requirements::css('summit/css/simple-sidebar.css');
        Requirements::css('themes/openstack/bower_assets/jquery-ui/themes/smoothness/jquery-ui.min.css');
        Requirements::css('themes/openstack/bower_assets/sweetalert/dist/sweetalert.css');
        Requirements::javascript('summit/javascript/simple-sidebar.js');
        Requirements::javascript('themes/openstack/javascript/bootstrap-paginator/src/bootstrap-paginator.js');
        Requirements::javascript('themes/openstack/bower_assets/sweetalert/dist/sweetalert.min.js');
        Requirements::javascript('themes/openstack/javascript/jquery-ajax-loader.js');
        Requirements::javascript('summit/javascript/summit-admin-schedule.js');

        $summit_id = intval($request->param('SummitID'));
        $summit    = Summit::get()->byID($summit_id);
        if(is_null($summit) || $summit->ID <= 0) return $this->httpError(404);

        return $this->getViewer('scheduleView')->process($this, array
            (
                'Summit'                    => $summit,
                'PresentationStatusOptions' => new ArrayList
                (
                    array
                    (
                        new ArrayData(array('Status'=> 'Non Received')),
                        new ArrayData(array('Status'=> Presentation::STATUS_RECEIVED))
                    )
                ),
                'PresentationSelectionStatusOptions' => new ArrayList
                (
                    array
                    (
                        //new ArrayData(array('Status'=> 'unaccepted')),
                        new ArrayData(array('Status'=> 'accepted')),
                        new ArrayData(array('Status'=> 'alternate')),
                    )
                ),
            )
        );
    }

    public function scheduleViewEditBulkAction(SS_HTTPRequest $request)
    {
        $summit_id = intval($request->param('SummitID'));
        $action    = $request->getVar('action');
        $type      = $request->getVar('type');
        $event_ids = $request->getVar('event_ids');

        if(empty($action)) throw new InvalidArgumentException('action');
        if(empty($type)) throw new InvalidArgumentException('type');
        if(empty($event_ids)) throw new InvalidArgumentException('event_ids');

        $event_ids = explode(',', $event_ids);



        $summit    = Summit::get()->byID($summit_id);
        if(is_null($summit) || $summit->ID <= 0) return $this->httpError(404);

        $events = new ArrayList();
        foreach($event_ids as $event_id)
        {
            $event = $this->event_repository->getById(intval($event_id));
            if(is_null($event) || $event->SummitID != $summit_id) continue;
            $events->push($event);
        }

        Requirements::css('summit/css/simple-sidebar.css');
        Requirements::css('themes/openstack/bower_assets/clockpicker/dist/bootstrap-clockpicker.min.css');
        Requirements::css('themes/openstack/bower_assets/sweetalert/dist/sweetalert.css');
        Requirements::css('themes/openstack/bower_assets/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css');
        Requirements::css('summit/css/bulk-actions.css');

        Requirements::javascript('summit/javascript/simple-sidebar.js');
        Requirements::javascript('themes/openstack/bower_assets/sweetalert/dist/sweetalert.min.js');
        Requirements::javascript('themes/openstack/javascript/jquery-ajax-loader.js');
        Requirements::javascript('openstack/code/utils/CustomHTMLFields/js/jquery-clockpicker.js');
        Requirements::javascript('themes/openstack/bower_assets/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js');
        Requirements::javascript('themes/openstack/bower_assets/moment/min/moment.min.js');
        Requirements::javascript('summit/javascript/summitapp-bulkactions.js');

        return $this->getViewer('scheduleViewEditBulkAction')->process($this, array
            (
                'Summit'            => $summit,
                'Events'            => $events,
                'UnpublishedEvents' => $type === 'unpublished',
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
        Requirements::css('themes/openstack/bower_assets/sweetalert/dist/sweetalert.css');
        Requirements::css('themes/openstack/bower_assets/jquery-ui/themes/smoothness/jquery-ui.css');
        Requirements::javascript('themes/openstack/bower_assets/sweetalert/dist/sweetalert.min.js');
        Requirements::javascript('summit/javascript/simple-sidebar.js');
        Requirements::javascript('themes/openstack/javascript/bootstrap-paginator/src/bootstrap-paginator.js');
        Requirements::javascript('themes/openstack/javascript/jquery-ajax-loader.js');
        Requirements::javascript('summit/javascript/jquery.tabletoCSV.js');

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

    public function speakers(SS_HTTPRequest $request)
    {
        $summit_id = intval($request->param('SummitID'));

        $summit = Summit::get()->byID($summit_id);

        Requirements::css('summit/css/simple-sidebar.css');
        // tag inputes
        Requirements::css('themes/openstack/bower_assets/bootstrap-tagsinput/dist/bootstrap-tagsinput.css');
        Requirements::css('themes/openstack/bower_assets/bootstrap-tagsinput/dist/bootstrap-tagsinput-typeahead.css');
        Requirements::css('themes/openstack/bower_assets/sweetalert/dist/sweetalert.css');
        Requirements::css('summit/css/summitapp-addspeaker.css');

        Requirements::javascript('summit/javascript/simple-sidebar.js');
        Requirements::javascript('themes/openstack/javascript/bootstrap-paginator/src/bootstrap-paginator.js');
        Requirements::javascript('themes/openstack/javascript/urlfragment.jquery.js');
        Requirements::javascript('themes/openstack/javascript/jquery-ajax-loader.js');
        Requirements::javascript('themes/openstack/bower_assets/sweetalert/dist/sweetalert.min.js');
        Requirements::javascript('themes/openstack/bower_assets/jquery-validate/dist/jquery.validate.min.js');
        Requirements::javascript('themes/openstack/bower_assets/jquery-validate/dist/additional-methods.min.js');
        Requirements::javascript('themes/openstack/bower_assets/typeahead.js/dist/typeahead.bundle.min.js');
        Requirements::javascript('themes/openstack/bower_assets/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js');
        Requirements::javascript('themes/openstack/javascript/jquery.cleanform.js');
        Requirements::javascript('summit/javascript/summitapp-addspeaker.js');

        return $this->getViewer('speakers')->process
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

    public function speakers_merge(SS_HTTPRequest $request)
    {
        $summit_id = intval($request->param('SummitID'));

        $summit = Summit::get()->byID($summit_id);

        Requirements::css('summit/css/simple-sidebar.css');
        // tag inputes
        Requirements::css('themes/openstack/bower_assets/bootstrap-tagsinput/dist/bootstrap-tagsinput.css');
        Requirements::css('themes/openstack/bower_assets/bootstrap-tagsinput/dist/bootstrap-tagsinput-typeahead.css');
        Requirements::css('themes/openstack/bower_assets/sweetalert/dist/sweetalert.css');
        Requirements::css('summit/css/summit-admin-speaker-merge.css');

        Requirements::javascript('summit/javascript/simple-sidebar.js');
        Requirements::javascript('themes/openstack/javascript/bootstrap-paginator/src/bootstrap-paginator.js');
        Requirements::javascript('themes/openstack/javascript/urlfragment.jquery.js');
        Requirements::javascript('themes/openstack/javascript/jquery-ajax-loader.js');
        Requirements::javascript('themes/openstack/bower_assets/sweetalert/dist/sweetalert.min.js');
        Requirements::javascript('themes/openstack/bower_assets/jquery-validate/dist/jquery.validate.min.js');
        Requirements::javascript('themes/openstack/bower_assets/jquery-validate/dist/additional-methods.min.js');
        Requirements::javascript('themes/openstack/bower_assets/typeahead.js/dist/typeahead.bundle.min.js');
        Requirements::javascript('themes/openstack/bower_assets/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js');
        Requirements::javascript('themes/openstack/javascript/jquery.cleanform.js');
        Requirements::javascript('summit/javascript/summit-admin-speaker-merge.js');

        return $this->getViewer('speakers_merge')->process
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

    public function editSpeaker(SS_HTTPRequest $request)
    {
        $summit_id  = intval($request->param('SummitID'));
        $summit     = Summit::get()->byID($summit_id);
        $speaker_id = intval($request->param('SpeakerID'));
        $speaker    = PresentationSpeaker::get()->byID($speaker_id);

        Requirements::css('summit/css/simple-sidebar.css');
        Requirements::css('summit/css/summit-admin-edit-speaker.css');
        Requirements::css('themes/openstack/bower_assets/chosen/chosen.min.css');
        Requirements::css('themes/openstack/bower_assets/sweetalert/dist/sweetalert.css');
        // tag input
        Requirements::css('themes/openstack/bower_assets/bootstrap-tagsinput/dist/bootstrap-tagsinput.css');
        Requirements::css('themes/openstack/bower_assets/bootstrap-tagsinput/dist/bootstrap-tagsinput-typeahead.css');
        Requirements::javascript('themes/openstack/bower_assets/sweetalert/dist/sweetalert.min.js');
        Requirements::javascript('themes/openstack/bower_assets/jquery-validate/dist/jquery.validate.min.js');
        Requirements::javascript('themes/openstack/bower_assets/jquery-validate/dist/additional-methods.min.js');
        Requirements::javascript('themes/openstack/bower_assets/chosen/chosen.jquery.min.js');
        Requirements::javascript('summit/javascript/simple-sidebar.js');
        Requirements::javascript('//tinymce.cachefly.net/4.3/tinymce.min.js');
        Requirements::javascript('themes/openstack/bower_assets/typeahead.js/dist/typeahead.bundle.min.js');
        Requirements::javascript('themes/openstack/bower_assets/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js');
        Requirements::javascript('summit/javascript/summitapp-editspeaker.js');

        return $this->getViewer('EditSpeaker')->process
        (
            $this->customise
            (
                array
                (
                    'Summit'   => $summit,
                    'Speaker' => $speaker,
                )
            )
        );
    }

    /**
     * @param string $type
     * @return bool
     */
    public function IsPresentationEventType($type){
        return SummitService::IsPresentationEventType($type);
    }

    /**
     * @param string $type
     * @return bool
     */
    public function IsSummitEventType($type){
        return SummitService::IsSummitEventType($type);
    }

    public function Time(){
        return time();
    }

    public function promocodes(SS_HTTPRequest $request)
    {
        $summit_id = intval($request->param('SummitID'));

        $summit = Summit::get()->byID($summit_id);
        $promocode_types = SummitRegistrationPromoCode::getTypes();

        Requirements::css('summit/css/simple-sidebar.css');
        // tag inputes
        Requirements::css('themes/openstack/bower_assets/bootstrap-tagsinput/dist/bootstrap-tagsinput.css');
        Requirements::css('themes/openstack/bower_assets/bootstrap-tagsinput/dist/bootstrap-tagsinput-typeahead.css');
        Requirements::css('themes/openstack/bower_assets/sweetalert/dist/sweetalert.css');
        Requirements::css('summit/css/summitapp-promocode.css');

        Requirements::javascript('summit/javascript/simple-sidebar.js');
        Requirements::javascript('themes/openstack/javascript/bootstrap-paginator/src/bootstrap-paginator.js');
        Requirements::javascript('themes/openstack/javascript/urlfragment.jquery.js');
        Requirements::javascript('themes/openstack/javascript/jquery-ajax-loader.js');
        Requirements::javascript('themes/openstack/bower_assets/sweetalert/dist/sweetalert.min.js');
        Requirements::javascript('themes/openstack/bower_assets/jquery-validate/dist/jquery.validate.min.js');
        Requirements::javascript('themes/openstack/bower_assets/jquery-validate/dist/additional-methods.min.js');
        Requirements::javascript('themes/openstack/bower_assets/typeahead.js/dist/typeahead.bundle.min.js');
        Requirements::javascript('themes/openstack/bower_assets/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js');
        Requirements::javascript('themes/openstack/javascript/jquery.cleanform.js');
        Requirements::javascript('summit/javascript/summitapp-promocode.js');

        return $this->getViewer('promocodes')->process
            (
                $this->customise
                    (
                        array
                        (
                            'Summit' => $summit,
                            'CodeTypes' => $promocode_types,
                        )
                    )
            );
    }

    public function editPromoCode(SS_HTTPRequest $request)
    {
        $summit_id  = intval($request->param('SummitID'));
        $summit     = Summit::get()->byID($summit_id);
        $code       = $request->param('Code');
        $promo_code = $this->promocode_repository->getByCode($summit_id, $code);
        $promocode_types = SummitRegistrationPromoCode::getTypes();

        Requirements::css('summit/css/simple-sidebar.css');
        Requirements::css('summit/css/summit-admin-edit-promocode.css');
        Requirements::css('themes/openstack/bower_assets/chosen/chosen.min.css');
        Requirements::css('themes/openstack/bower_assets/sweetalert/dist/sweetalert.css');
        Requirements::css('themes/openstack/bower_assets/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css');
        // tag input
        Requirements::css('themes/openstack/bower_assets/bootstrap-tagsinput/dist/bootstrap-tagsinput.css');
        Requirements::css('themes/openstack/bower_assets/bootstrap-tagsinput/dist/bootstrap-tagsinput-typeahead.css');
        Requirements::javascript('themes/openstack/bower_assets/sweetalert/dist/sweetalert.min.js');
        Requirements::javascript('themes/openstack/bower_assets/jquery-validate/dist/jquery.validate.min.js');
        Requirements::javascript('themes/openstack/bower_assets/jquery-validate/dist/additional-methods.min.js');
        Requirements::javascript('themes/openstack/bower_assets/chosen/chosen.jquery.min.js');
        Requirements::javascript('summit/javascript/simple-sidebar.js');
        Requirements::javascript('//tinymce.cachefly.net/4.3/tinymce.min.js');
        Requirements::javascript('themes/openstack/bower_assets/typeahead.js/dist/typeahead.bundle.min.js');
        Requirements::javascript('themes/openstack/bower_assets/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js');
        Requirements::javascript('summit/javascript/summitapp-editpromocode.js');

        return $this->getViewer('editPromoCode')->process
            (
                $this->customise
                    (
                        array
                        (
                            'Summit'         => $summit,
                            'PromoCode'      => $promo_code,
                            'PromoCodeTypes' => $promocode_types,
                        )
                    )
            );
    }

    public function promocodes_sponsors(SS_HTTPRequest $request)
    {
        $summit_id = intval($request->param('SummitID'));
        $summit = Summit::get()->byID($summit_id);
        $promocodes = $this->promocode_repository->getGroupedBySponsor($summit_id);

        Requirements::css('summit/css/simple-sidebar.css');
        // tag inputes
        Requirements::css('themes/openstack/bower_assets/bootstrap-tagsinput/dist/bootstrap-tagsinput.css');
        Requirements::css('themes/openstack/bower_assets/bootstrap-tagsinput/dist/bootstrap-tagsinput-typeahead.css');
        Requirements::css('themes/openstack/bower_assets/sweetalert/dist/sweetalert.css');
        Requirements::css('summit/css/summitapp-promocode.css');

        Requirements::javascript('summit/javascript/simple-sidebar.js');
        Requirements::javascript('themes/openstack/javascript/bootstrap-paginator/src/bootstrap-paginator.js');
        Requirements::javascript('themes/openstack/javascript/urlfragment.jquery.js');
        Requirements::javascript('themes/openstack/javascript/jquery-ajax-loader.js');
        Requirements::javascript('themes/openstack/bower_assets/sweetalert/dist/sweetalert.min.js');
        Requirements::javascript('themes/openstack/bower_assets/jquery-validate/dist/jquery.validate.min.js');
        Requirements::javascript('themes/openstack/bower_assets/jquery-validate/dist/additional-methods.min.js');
        Requirements::javascript('themes/openstack/bower_assets/typeahead.js/dist/typeahead.bundle.min.js');
        Requirements::javascript('themes/openstack/bower_assets/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js');
        Requirements::javascript('themes/openstack/javascript/jquery.cleanform.js');
        Requirements::javascript('summit/javascript/summitapp-promocode.js');

        return $this->getViewer('promocodes_sponsors')->process
            (
                $this->customise
                    (
                        array
                        (
                            'Summit' => $summit,
                            'PromoCodes' => $promocodes,
                        )
                    )
            );
    }

    public function editPromoCodeSponsor(SS_HTTPRequest $request)
    {
        $summit_id  = intval($request->param('SummitID'));
        $summit     = Summit::get()->byID($summit_id);
        $sponsor_id = $request->param('SponsorID');
        $sponsor    = (is_numeric($sponsor_id)) ? Company::get_by_id('Company',$sponsor_id) : null;
        $promocodes = (is_numeric($sponsor_id)) ? $this->promocode_repository->getBySponsor($summit_id, $sponsor_id) : array();

        Requirements::css('summit/css/simple-sidebar.css');
        Requirements::css('summit/css/summit-admin-edit-promocode.css');
        Requirements::css('themes/openstack/bower_assets/chosen/chosen.min.css');
        Requirements::css('themes/openstack/bower_assets/sweetalert/dist/sweetalert.css');
        Requirements::css('themes/openstack/bower_assets/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css');
        // tag input
        Requirements::css('themes/openstack/bower_assets/bootstrap-tagsinput/dist/bootstrap-tagsinput.css');
        Requirements::css('themes/openstack/bower_assets/bootstrap-tagsinput/dist/bootstrap-tagsinput-typeahead.css');
        Requirements::javascript('themes/openstack/bower_assets/sweetalert/dist/sweetalert.min.js');
        Requirements::javascript('themes/openstack/bower_assets/jquery-validate/dist/jquery.validate.min.js');
        Requirements::javascript('themes/openstack/bower_assets/jquery-validate/dist/additional-methods.min.js');
        Requirements::javascript('themes/openstack/bower_assets/chosen/chosen.jquery.min.js');
        Requirements::javascript('summit/javascript/simple-sidebar.js');
        Requirements::javascript('//tinymce.cachefly.net/4.3/tinymce.min.js');
        Requirements::javascript('themes/openstack/bower_assets/typeahead.js/dist/typeahead.bundle.min.js');
        Requirements::javascript('themes/openstack/bower_assets/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js');
        Requirements::javascript('summit/javascript/summitapp-editpromocode-sponsor.js');

        return $this->getViewer('editPromoCodeSponsor')->process
            (
                $this->customise
                    (
                        array
                        (
                            'Summit'         => $summit,
                            'PromoCodes'     => $promocodes,
                            'Sponsor'        => $sponsor,
                        )
                    )
            );
    }

    public function promocodes_bulk(SS_HTTPRequest $request)
    {
        $summit_id  = intval($request->param('SummitID'));
        $summit     = Summit::get()->byID($summit_id);
        $promocode_types = SummitRegistrationPromoCode::getTypes();

        Requirements::css('summit/css/simple-sidebar.css');
        Requirements::css('summit/css/summit-admin-promocodes-bulk.css');
        Requirements::css('themes/openstack/bower_assets/chosen/chosen.min.css');
        Requirements::css('themes/openstack/bower_assets/sweetalert/dist/sweetalert.css');
        Requirements::css('themes/openstack/bower_assets/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css');
        // tag input
        Requirements::css('themes/openstack/bower_assets/bootstrap-tagsinput/dist/bootstrap-tagsinput.css');
        Requirements::css('themes/openstack/bower_assets/bootstrap-tagsinput/dist/bootstrap-tagsinput-typeahead.css');
        Requirements::javascript('themes/openstack/bower_assets/sweetalert/dist/sweetalert.min.js');
        Requirements::javascript('themes/openstack/bower_assets/jquery-validate/dist/jquery.validate.min.js');
        Requirements::javascript('themes/openstack/bower_assets/jquery-validate/dist/additional-methods.min.js');
        Requirements::javascript('themes/openstack/bower_assets/chosen/chosen.jquery.min.js');
        Requirements::javascript('summit/javascript/simple-sidebar.js');
        Requirements::javascript('//tinymce.cachefly.net/4.3/tinymce.min.js');
        Requirements::javascript('themes/openstack/bower_assets/typeahead.js/dist/typeahead.bundle.min.js');
        Requirements::javascript('themes/openstack/bower_assets/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js');
        Requirements::javascript('summit/javascript/summitapp-promocodes-bulk.js');

        return $this->getViewer('promocodes_bulk')->process
            (
                $this->customise(array(
                    'Summit' => $summit,
                    'CodeTypes' => $promocode_types
                ))
            );
    }

    public function events_bulk(SS_HTTPRequest $request)
    {
        $summit_id = intval($request->param('SummitID'));
        $summit = Summit::get()->byID($summit_id);

        Requirements::css('summit/css/simple-sidebar.css');
        Requirements::css('themes/openstack/bower_assets/sweetalert/dist/sweetalert.css');
        Requirements::css('themes/openstack/bower_assets/jquery-ui/themes/smoothness/jquery-ui.css');
        Requirements::javascript('themes/openstack/bower_assets/sweetalert/dist/sweetalert.min.js');
        Requirements::javascript('summit/javascript/simple-sidebar.js');
        Requirements::javascript('themes/openstack/javascript/bootstrap-paginator/src/bootstrap-paginator.js');
        Requirements::javascript('themes/openstack/javascript/jquery-ajax-loader.js');

        return $this->getViewer('events_bulk')->process
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

}