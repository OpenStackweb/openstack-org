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
final class SummitEventAdminController extends Controller
{

    /**
     * @var ISummitEventRepository
     */
    private $event_repository;

    /**
     * @var  SummitAppAdminController The parent controller
     */
    protected $parent;

    private static $allowed_actions = array
    (
        'scheduleView',
        'publishedEvents',
        'pendingEvents',
        'scheduleViewEditBulkAction',
        'editPresentationList',
        'presentationLists',
        'eventsBulk',
        'editEvent',
    );

    private static $url_handlers = array
    (
        'schedule'                                 => 'scheduleView',
        'published'                                => 'publishedEvents',
        'unpublished'                              => 'pendingEvents',
        'bulk-action'                              => 'scheduleViewEditBulkAction',
        'presentation-lists/$PresentationListId!'  => 'editPresentationList',
        'presentation-lists'                       => 'presentationLists',
        'bulk'                                     => 'eventsBulk',
        '$EventID'                                 => 'editEvent',
    );

    /**
     * SummitEventAdminController constructor.
     * @param SummitAppAdminController $parent
     */
    public function __construct(SummitAppAdminController $parent)
    {
        parent::__construct();
        $this->parent       = $parent;
        $this->event_repository = new SapphireSummitEventRepository();
    }

    public function Link($action = null)
    {
        return $this->parent->Link($action);
    }

    /**
     * @param string $type
     * @return bool
     */
    public function IsPresentationEventType($type){
        return PresentationType::IsPresentationEventType($type);
    }

    /**
     * @param string $type
     * @param bool $allows_attachment
     * @return int
     */
    public function getTypeTaxonomy($type, $allows_attachment){
        if(PresentationType::IsPresentationEventType($type))
            return ISummitEventTypeTaxonomy::Presentation;
        if(SummitEventType::isPrivate($type))
            return ISummitEventTypeTaxonomy::GroupEvent;
        if($allows_attachment)
            return ISummitEventTypeTaxonomy::EventWithFile;
        return ISummitEventTypeTaxonomy::Event;
    }

    public function publishedEvents(SS_HTTPRequest $request)
    {
        $summit_id = intval($request->param('SummitID'));

        $summit = Summit::get()->byID($summit_id);

        Requirements::css('summit/css/simple-sidebar.css');
        Requirements::javascript('summit/javascript/simple-sidebar.js');

        $events = $summit->Events()->filter('Published', true);

        return $this->parent->getViewer('publishedEvents')->process
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

        return $this->parent->getViewer('pendingEvents')->process
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

        return $this->parent->getViewer('presentationLists')->process
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


        return $this->parent->getViewer('editPresentationList')->process
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

    public function editEvent(SS_HTTPRequest $request)
    {
        $summit_id = intval($request->param('SummitID'));
        $summit    = Summit::get()->byID($summit_id);
        $event_id  = intval($request->param('EventID'));
        $event     = ($event_id == 0) ? null : SummitEvent::get()->byID($event_id);

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

        return $this->parent->getViewer('EditEvent')->process
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

        return $this->parent->getViewer('scheduleView')->process($this, array
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
                        new ArrayData(array('Status'=> 'lightning accepted')),
                        new ArrayData(array('Status'=> 'lightning alternate')),
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

        return $this->parent->getViewer('scheduleViewEditBulkAction')->process($this, array
            (
                'Summit'            => $summit,
                'Events'            => $events,
                'UnpublishedEvents' => $type === 'unpublished',
            )
        );
    }

    public function eventsBulk(SS_HTTPRequest $request)
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

        return $this->parent->getViewer('events_bulk')->process
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