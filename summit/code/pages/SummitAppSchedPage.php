<?php

/**
 * Class SummitAppSchedPage
 */
class SummitAppSchedPage extends SummitPage
{

}

/**
 * Class SummitAppSchedPage_Controller
 */
class SummitAppSchedPage_Controller extends SummitPage_Controller
{

    /**
     * @var ISpeakerRepository
     */
    private $speaker_repository;

    /**
     * @var ISummitEventRepository
     */
    private $event_repository;

    /**
     * @return ISpeakerRepository
     */
    public function getSpeakerRepository()
    {
        return $this->speaker_repository;
    }

    /**
     * @param ISpeakerRepository $speaker_repository
     */
    public function setSpeakerRepository(ISpeakerRepository $speaker_repository)
    {
        $this->speaker_repository = $speaker_repository;
    }

    /**
     * @return ISummitEventRepository
     */
    public function getEventRepository()
    {
        return $this->event_repository;
    }

    /**
     * @param ISummitEventRepository $event_repository
     */
    public function setEventRepository(ISummitEventRepository $event_repository)
    {
        $this->event_repository = $event_repository;
    }

    static $allowed_actions = array(
        'ViewEvent',
        'ViewSpeakerProfile',
        'ViewAttendeeProfile',
        'DoGlobalSearch',
        'index',
    );

    static $url_handlers = array
    (
        'events/$EVENT_ID/$EVENT_TITLE' => 'ViewEvent',
        'speakers/$SPEAKER_ID' => 'ViewSpeakerProfile',
        'attendees/$ATTENDEE_ID' => 'ViewAttendeeProfile',
        'global-search' => 'DoGlobalSearch',
    );

    public function init()
    {

        $this->top_section = 'full';

        parent::init();
        Requirements::css('themes/openstack/bower_assets/jquery-loading/dist/jquery.loading.min.css');
        Requirements::css('themes/openstack/bower_assets/chosen/chosen.min.css');
        Requirements::css("summit/css/schedule-grid.css");
        Requirements::javascript('themes/openstack/javascript/jquery-ajax-loader.js');
        Requirements::javascript('themes/openstack/bower_assets/chosen/chosen.jquery.min.js');
        Requirements::javascript('themes/openstack/bower_assets/jquery-validate/dist/jquery.validate.min.js');
        Requirements::javascript('themes/openstack/bower_assets/jquery-validate/dist/additional-methods.min.js');
        Requirements::javascript('themes/openstack/javascript/urlfragment.jquery.js');
    }

    public function ViewEvent()
    {
        $event_id = intval($this->request->param('EVENT_ID'));
        $this->event_id = $event_id;
        $event = $this->event_repository->getById($event_id);

        if (!isset($event)) {
            return $this->httpError(404, 'Sorry that article could not be found');
        }

        Requirements::block("summit/css/schedule-grid.css");
        Requirements::css("summit/css/summitapp-event.css");

        return $this->renderWith(array('SummitAppEventPage', 'SummitPage', 'Page'), array('Event' => $event));
    }

    public function ViewSpeakerProfile()
    {
        $speaker_id = intval($this->request->param('SPEAKER_ID'));
        $speaker = $this->speaker_repository->getById($speaker_id);

        if (!isset($speaker)) {
            return $this->httpError(404, 'Sorry that speaker could not be found');
        }

        //Requirements::block("summit/css/schedule-grid.css");
        Requirements::css("summit/css/summitapp-speaker.css");

        return $this->renderWith(array('SummitAppSpeakerPage', 'SummitPage', 'Page'),
            array
            (
                'Speaker' => $speaker,
                'Summit' => $this->Summit()
            )
        );
    }

    public function ViewAttendeeProfile()
    {
        // TODO : implement view
        $attendee_id = intval($this->request->param('ATTENDEE_ID'));

        return $this->httpError(404, 'Sorry that attendee could not be found');
    }

    public function getFeedbackForm()
    {
        Requirements::javascript(Director::protocol() . "ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js");
        Requirements::javascript(Director::protocol() . "ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/additional-methods.min.js");
        Requirements::javascript("marketplace/code/ui/frontend/js/star-rating.min.js");
        Requirements::javascript("summit/javascript/summitapp-feedbackform.js");
        Requirements::css("marketplace/code/ui/frontend/css/star-rating.min.css");
        $form = new SummitEventFeedbackForm($this, 'SummitEventFeedbackForm');

        return $form;
    }

    public function isEventOnMySchedule($event_id)
    {
        return SummitAppScheduleApi::isEventOnMySchedule($event_id, $this->Summit());
    }

    public function DoGlobalSearch(SS_HTTPRequest $request)
    {
        $term = Convert::raw2sql($request->requestVar('t'));
        if (empty($term)) {
            return $this->httpError(404);
        }

        $summit_id = $this->Summit()->ID;

        $db_term = SummitScheduleGlobalSearchTerm::get()->filter(array(
            'Term' => $term,
            'SummitID' => $summit_id
        ))->first();
        if (is_null($db_term)) {
            $db_term = SummitScheduleGlobalSearchTerm::create();
        }
        $db_term->Hits = intval($db_term->Hits) + 1;
        $db_term->Term = $term;
        $db_term->SummitID = $summit_id;
        $db_term->write();

        $popular_terms = SummitScheduleGlobalSearchTerm::get()->filter(array('SummitID' => $summit_id))->sort('Term',
            'ASC')->limit(25, 0);

        $speakers = $this->speaker_repository->searchBySummitAndTerm($this->Summit(), $term);
        $events = $this->event_repository->searchBySummitAndTerm($this->Summit(), $term);

        return $this->renderWith
        (
            array
            (
                'SummitAppSchedPage_globalSearchResults',
                'SummitPage',
                'Page'
            ),
            array
            (
                'SpeakerResults' => new ArrayList($speakers),
                'EventResults' => new ArrayList($events),
                'SearchTerm' => $term,
                'PopularTerms' => $popular_terms,
            )
        );
    }

    public function getPresentationLevels()
    {
        return Presentation::getLevels();
    }
}