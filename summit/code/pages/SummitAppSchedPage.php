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
        'ViewMySchedule',
        'ExportMySchedule',
        'DoGlobalSearch',
        'index',
    );

    static $url_handlers = array
    (
        'events/$EVENT_ID/$EVENT_TITLE' => 'ViewEvent',
        'speakers/$SPEAKER_ID' => 'ViewSpeakerProfile',
        'attendees/$ATTENDEE_ID' => 'ViewAttendeeProfile',
        'mine/pdf' => 'ExportMySchedule',
        'mine' => 'ViewMySchedule',
        'global-search' => 'DoGlobalSearch',
    );

    public function init()
    {

        $this->top_section = 'short'; //or full

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
        $goback = $this->getRequest()->getVar('goback') ? $this->getRequest()->getVar('goback') : '';

        if (is_null($event) || !$event->isPublished()) {
            return $this->httpError(404, 'Sorry that event could not be found');
        }

        Requirements::block("summit/css/schedule-grid.css");
        Requirements::css("summit/css/summitapp-event.css");
        Requirements::javascript("summit/javascript/schedule/event-detail-page.js");

        $token = Session::get("SummitAppEventPage.ShareEmail");
        if (!$token) {
            $token = md5(uniqid(rand(), TRUE));
            Session::set("SummitAppEventPage.ShareEmail",$token);
            Session::set("SummitAppEventPage.ShareEmailCount",0);
        }

        return $this->renderWith(
            array('SummitAppEventPage', 'SummitPage', 'Page'),
            array(
                'Event' => $event,
                'FB_APP_ID' => FB_APP_ID,
                'goback' => $goback,
                'Token' => $token
            ));
    }

    public function ViewMySchedule()
    {
        $member    = Member::currentUser();
        $goback = $this->getRequest()->getVar('goback') ? $this->getRequest()->getVar('goback') : '';

        if (is_null($this->Summit())) return $this->httpError(404, 'Sorry, summit not found');

        if(is_null($member) || !$member->isAttendee($this->Summit()->ID)) return $this->httpError(401, 'You need to login to access your schedule.');

        $my_schedule = $member->getSummitAttendee($this->Summit()->ID)->Schedule()->sort(array('StartDate'=>'ASC','Location.Name' => 'ASC'));

        return $this->renderWith(
            array('SummitAppMySchedulePage', 'SummitPage', 'Page'),
            array(
                'Schedule' => $my_schedule,
                'Summit'   => $this->Summit(),
                'goback'   => $goback));
    }

    public function ExportMySchedule()
    {
        $member = Member::currentUser();
        $base = Director::protocolAndHost();

        if (is_null($this->Summit())) return $this->httpError(404, 'Sorry, summit not found');

        if(is_null($member) || !$member->isAttendee($this->Summit()->ID)) return $this->httpError(401, 'You need to login to access your schedule.');

        $my_schedule = $member->getSummitAttendee($this->Summit()->ID)->Schedule()->sort(array('StartDate'=>'ASC','Location.Name' => 'ASC'));
        $day_schedule = new ArrayList();
        foreach ($my_schedule as $event) {
            $day_label = $event->getDayLabel();
            if ($day_array = $day_schedule->find('Day',$day_label)) {
                $day_array->Events->push($event);
            } else {
                $day_array = new ArrayData(array('Day' => $day_label, 'Events' => new ArrayList()));
                $day_array->Events->push($event);
                $day_schedule->push($day_array);
            }
        }

        $html_inner = $this->renderWith(
            array('SummitAppMySchedulePage_pdf'),
            array('Schedule' => $day_schedule, 'Summit' => $this->Summit()));

        $css = @file_get_contents($base . "/summit/css/summitapp-myschedule-pdf.css");

        //create pdf
        $file = FileUtils::convertToFileName('my-schedule') . '.pdf';

        $html_outer = sprintf("<html><head><style>%s</style></head><body><div class='container'>%s</div></body></html>",
            $css, $html_inner);

        //Requirements::css("/summit/css/summitapp-myschedule-pdf.css");
        //return $html_outer;

        try {
            $html2pdf = new HTML2PDF('P', 'A4', 'en', true, 'UTF-8', array(15, 5, 15, 5));
            $html2pdf->setTestIsImage(false);
            $html2pdf->WriteHTML($html_outer);

            //clean output buffer
            ob_end_clean();
            $html2pdf->Output($file, "D");
        } catch (HTML2PDF_exception $e) {
            $message = array(
                'errno' => '',
                'errstr' => $e->__toString(),
                'errfile' => 'SummitAppSchedPage.php',
                'errline' => '',
                'errcontext' => ''
            );
            SS_Log::log($message, SS_Log::ERR);
            $this->httpError(404, 'There was an error on PDF generation!');
        }
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

    public function DoGlobalSearch(SS_HTTPRequest $request)
    {
        $term = Convert::raw2sql($request->requestVar('t'));
        if (empty($term)) {
            return $this->httpError(404);
        }

        $summit_id = $this->Summit()->ID;


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
                'EventResults'   => new ArrayList($events),
                'SearchTerm'     => $term,
            )
        );
    }

    public function getPresentationLevels()
    {
        return Presentation::getLevels();
    }
}