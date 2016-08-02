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
     * @var IRSVPRepository
     */
    private $rsvp_repository;

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

    /**
     * @return IRSVPRepository
     */
    public function getRSVPRepository()
    {
        return $this->rsvp_repository;
    }

    /**
     * @param IRSVPRepository $rsvp_repository
     */
    public function setRSVPRepository(IRSVPRepository $rsvp_repository)
    {
        $this->rsvp_repository = $rsvp_repository;
    }


    static $allowed_actions = array(
        'ViewEvent',
        'ViewSpeakerProfile',
        'ViewAttendeeProfile',
        'ViewMySchedule',
        'ExportMySchedule',
        'ExportEventToICS',
        'DoGlobalSearch',
        'index',
        'ViewFullSchedule',
        'ExportFullSchedule',
        'eventDetails'
    );

    static $url_handlers = array
    (
        'events/$EVENT_ID/html'         => 'eventDetails',
        'events/$EVENT_ID/export_ics'   => 'ExportEventToICS',
        'events/$EVENT_ID/$EVENT_TITLE' => 'ViewEvent',
        'speakers/$SPEAKER_ID'          => 'ViewSpeakerProfile',
        'attendees/$ATTENDEE_ID'        => 'ViewAttendeeProfile',
        'mine/pdf'                      => 'ExportMySchedule',
        'mine'                          => 'ViewMySchedule',
        'full/pdf'                      => 'ExportFullSchedule',
        'full'                          => 'ViewFullSchedule',
        'global-search'                 => 'DoGlobalSearch',
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
        Requirements::javascript('themes/openstack/bower_assets/pure-templates/libs/pure.min.js');
        // GOOGLE CALENDAR
        Requirements::customScript(" var CLIENT_ID = '".GAPI_CLIENT."';");
        Requirements::javascript('summit/javascript/schedule/google-calendar.js');
        Requirements::javascript('https://apis.google.com/js/client.js?onload=checkAuth');

    }

    public function ViewEvent(SS_HTTPRequest $request)
    {
        $event  = $this->getSummitEntity($request);

        $goback = $this->getRequest()->postVar('goback') ? $this->getRequest()->postVar('goback') : '';

        if (is_null($event) || !$event->isPublished()) {
            return $this->httpError(404, 'Sorry that event could not be found');
        }

        // only send meta tags
        if($request->getHeader("Prefer-Html-Meta-Tags")){
            return $this->buildOnlyMetaTagsResponse($event);
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
                'Event'     => $event,
                'FB_APP_ID' => FB_APP_ID,
                'goback'    => $goback,
                'Token'     => $token
            ));
    }

    public function eventDetails(SS_HTTPRequest $request)
    {
        if(!Director::is_ajax())
            return $this->httpError(404, 'Sorry that event could not be found');

        $event_id = intval($request->param('EVENT_ID'));
        $event    = $this->event_repository->getById($event_id);
        if (is_null($event) || !$event->isPublished()) {
            return $this->httpError(404, 'Sorry that event could not be found');
        }

        return $this->renderWith
        (
            array
            (
                'SummitAppSchedPage_eventDetails'
            ),
            array
            (
                'Event' => $event,
            )
        );
    }

    /**
     * @return Form|string
     * @throws NotFoundEntityException
     */
    public function RSVPForm($event_id)
    {
        $event = $this->event_repository->getById($event_id);
        $rsvp_template  = $event->RSVPTemplate();
        $rsvp = $this->rsvp_repository->getByEventAndSubmitter($event_id,Member::currentUserID());
        $builder = new RSVPTemplateUIBuilder();
        $form = $builder->build($rsvp_template,$rsvp,$event);
        return $form;
    }

    public function ExportEventToICS(SS_HTTPRequest $request)
    {
        $event_id            = intval($request->param('EVENT_ID'));

        $event  = $this->event_repository->getById($event_id);
        if(is_null($event)) throw new NotFoundEntityException('SummitEvent', sprintf(' id %s', $event_id));


        $ical = "BEGIN:VCALENDAR
PRODID:-//hacksw/handcal//NONSGML v1.0//EN
VERSION:2.0
CALSCALE:GREGORIAN
METHOD:PUBLISH
BEGIN:VEVENT
UID:" . md5(uniqid(mt_rand(), true)) . "event
DTSTAMP:" . gmdate('Ymd').'T'. gmdate('His') . "Z
DTSTART:".date('Ymd',strtotime($event->getField('StartDate')))."T".date('His',strtotime($event->getField('StartDate')))."Z
DTEND:".date('Ymd',strtotime($event->getField('EndDate')))."T".date('His',strtotime($event->getField('EndDate')))."Z
SUMMARY:".$event->Title."
DESCRIPTION:".strip_tags($event->ShortDescription)."
X-ALT-DESC:".$event->ShortDescription."
END:VEVENT
END:VCALENDAR";

        //set correct content-type-header
        header('Content-type: text/calendar; charset=utf-8');
        header('Content-Disposition: inline; filename=event-'.$event_id.'.ics');
        echo $ical;

        exit();
    }

    public function ViewMySchedule()
    {
        $member    = Member::currentUser();
        $goback = $this->getRequest()->postVar('goback') ? $this->getRequest()->postVar('goback') : '';

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

    public function ViewFullSchedule()
    {
        $goback = $this->getRequest()->postVar('goback') ? $this->getRequest()->postVar('goback') : '';

        if (is_null($this->Summit())) return $this->httpError(404, 'Sorry, summit not found');

        $schedule = $this->Summit()->getSchedule();

        return $this->renderWith(
            array('SummitAppFullSchedulePage', 'SummitPage', 'Page'),
            array(
                'Schedule' => $schedule,
                'Summit'   => $this->Summit(),
                'goback'   => $goback));
    }

    public function ExportMySchedule()
    {
        $member = Member::currentUser();
        $base = Director::protocolAndHost();
        $show_desc = $this->getRequest()->getVar('show_desc') ? $this->getRequest()->getVar('show_desc') : false;


        if (is_null($this->Summit())) return $this->httpError(404, 'Sorry, summit not found');

        if(is_null($member) || !$member->isAttendee($this->Summit()->ID)) return $this->httpError(401, 'You need to login to access your schedule.');

        $my_schedule = $member->getSummitAttendee($this->Summit()->ID)->Schedule()->sort(array('StartDate'=>'ASC','Location.Name' => 'ASC'));
        $day_schedule = new ArrayList();
        foreach ($my_schedule as $event) {
            $day_label = $event->getDayLabel();
            if ($day_array = $day_schedule->find('Group',$day_label)) {
                $day_array->Events->push($event);
            } else {
                $day_array = new ArrayData(array('Group' => $day_label, 'Events' => new ArrayList()));
                $day_array->Events->push($event);
                $day_schedule->push($day_array);
            }
        }

        $html_inner = $this->renderWith(
            array('SummitAppMySchedulePage_pdf'),
            array(
                'Schedule' => $day_schedule,
                'Summit' => $this->Summit(),
                'ShowDescription' => $show_desc,
                'Heading' => 'My Schedule'));

        $css = @file_get_contents($base . "/summit/css/summitapp-myschedule-pdf.css");

        //create pdf
        $file = FileUtils::convertToFileName('my-schedule') . '.pdf';

        $html_outer = sprintf("<html><head><style>%s</style></head><body><div class='container'>%s</div></body></html>",
            $css, $html_inner);

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

    public function ExportFullSchedule()
    {
        $sort = $this->getRequest()->getVar('sort') ? $this->getRequest()->getVar('sort') : 'day';
        $show_desc = $this->getRequest()->getVar('show_desc') ? $this->getRequest()->getVar('show_desc') : false;
        $base = Director::protocolAndHost();

        if (is_null($this->Summit())) return $this->httpError(404, 'Sorry, summit not found');

        $schedule = $this->Summit()->getSchedule();
        $events = new ArrayList();
        $sort_list = false;

        foreach ($schedule as $event) {
            switch ($sort) {
                case 'day':
                    $group_label = $event->getDayLabel();
                    break;
                case 'track':
                    if (!$event->isPresentation() || !$event->Category() || !$event->Category()->Title) {continue 2;}
                    $group_label = $event->Category()->Title;
                    $sort_list = true;
                    break;
                case 'event_type':
                    $group_label = $event->Type->Type;
                    $sort_list = true;
                    break;
            }

            if ($group_array = $events->find('Group',$group_label)) {
                $group_array->Events->push($event);
            } else {
                $group_array = new ArrayData(array('Group' => $group_label, 'Events' => new ArrayList()));
                $group_array->Events->push($event);
                $events->push($group_array);
            }
        }

        if ($sort_list)
            $events->sort('Group');

        $html_inner = $this->renderWith(
            array('SummitAppMySchedulePage_pdf'),
            array(  'Schedule' => $events,
                    'Summit' => $this->Summit(),
                    'ShowDescription' => $show_desc,
                    'Heading' => 'Full Schedule by '.$sort));

        $css = @file_get_contents($base . "/summit/css/summitapp-myschedule-pdf.css");

        //create pdf
        $file = FileUtils::convertToFileName('full-schedule') . '.pdf';

        $html_outer = sprintf("<html><head><style>%s</style></head><body><div class='container'>%s</div></body></html>",
            $css, $html_inner);

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

    /**
     * @param SS_HTTPRequest $request
     * @return IEntity|null
     */
    private function getSummitEntity(SS_HTTPRequest $request){
        $speaker_id = intval($request->param('SPEAKER_ID'));
        $event_id   = intval($request->param('EVENT_ID'));

        if($event_id > 0) {
            $this->event_id = $event_id;
            return $this->event_repository->getById($event_id);
        }
        if($speaker_id > 0){
            return $this->speaker_repository->getById($speaker_id);
        }

        return null;
    }

    public function ViewSpeakerProfile(SS_HTTPRequest $request)
    {
        $speaker  = $this->getSummitEntity($request);

        if (!isset($speaker)) {
            return $this->httpError(404, 'Sorry that speaker could not be found');
        }

        // only send meta tags
        if($request->getHeader("Prefer-Html-Meta-Tags")){
            return $this->buildOnlyMetaTagsResponse($speaker);
        }

        //Requirements::block("summit/css/schedule-grid.css");
        Requirements::css("summit/css/summitapp-speaker.css");

        return $this->renderWith(array('SummitAppSpeakerPage', 'SummitPage', 'Page'),
            array
            (
                'Speaker' => $speaker,
                'Summit'  => $this->Summit(),
            )
        );
    }

    /**
     * @param $entity
     * @return SS_HTTPResponse
     */
    private function buildOnlyMetaTagsResponse($entity){
        $response = new SS_HTTPResponse();
        $response->setStatusCode(200);
        $html = <<< APP_LINKS
               <html>
                <head>
                    {$entity->MetaTags()}
                </head>
                <body>
                </body>
                </html>
APP_LINKS;
        $response->setBody($html);
        return $response;
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

    public function MetaTags()
    {
        $entity = $this->getSummitEntity(Controller::curr()->getRequest());
        if(!is_null($entity)){
            return $entity->MetaTags();
        }
        $tags = parent::MetaTags(false);
        // IOS
        $tags .= AppLinkIOSMetadataBuilder::buildAppLinksMetaTags($tags, "schedule");
        // Android
        $tags .= AppLinkIAndroidMetadataBuilder::buildAppLinksMetaTags($tags, "schedule");
        return $tags;
    }
}