<?php

/**
 * Class SummitAppSchedPage
 */
class SummitAppSchedPage extends SummitPage
{
    private static $db = array
    (
        'EnableMobileSupport' => 'Boolean',
    );

    /**
     * @param ISummit $summit
     * @return SummitAppSchedPage
     */
    static public function getBy(ISummit $summit){
        $page = Versioned::get_by_stage('SummitAppSchedPage', 'Live')->filter('SummitID', $summit->getIdentifier())->first();
        if(is_null($page))
            $page = Versioned::get_by_stage('SummitAppSchedPage', 'Stage')->filter('SummitID', $summit->getIdentifier())->first();
        return $page;
    }

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->addFieldToTab('Root.Main', new CheckboxField('EnableMobileSupport', 'Enable Mobile App Download Dialog'));
        return $fields;
    }
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

    private $eventfeedback_repository;

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

    public function setEventFeedbackRepository(IEventFeedbackRepository $eventfeedback_repository)
    {
        $this->eventfeedback_repository = $eventfeedback_repository;
    }

    static $allowed_actions = array(
        'ViewEvent',
        'ViewSpeakerProfile',
        'ViewAttendeeProfile',
        'ViewMySchedule',
        'ExportMySchedule',
        'DoGlobalSearch',
        'index',
        'ViewFullSchedule',
        'ExportFullSchedule',
        'eventDetails',
        'ViewEventRSVP',
    );

    static $url_handlers = array
    (
        'events/$EVENT_ID/html'              => 'eventDetails',
        'events/$EVENT_ID/$EVENT_TITLE/rsvp' => 'ViewEventRSVP',
        'events/$EVENT_ID/$EVENT_TITLE'      => 'ViewEvent',
        'speakers/$SPEAKER_ID'               => 'ViewSpeakerProfile',
        'attendees/$ATTENDEE_ID'             => 'ViewAttendeeProfile',
        'mine/pdf'                           => 'ExportMySchedule',
        'mine'                               => 'ViewMySchedule',
        'full/pdf'                           => 'ExportFullSchedule',
        'full'                               => 'ViewFullSchedule',
        'global-search'                      => 'DoGlobalSearch',
    );

    public function init()
    {

        $this->top_section = 'short'; //or full

        parent::init();
        Requirements::css('themes/openstack/bower_assets/jquery-loading/dist/jquery.loading.min.css');
        Requirements::css('themes/openstack/bower_assets/chosen/chosen.min.css');
        Requirements::css('summit/css/install_mobile_app.css');
        Requirements::css("summit/css/schedule-grid.css");
        Requirements::css('themes/openstack/bower_assets/sweetalert2/dist/sweetalert2.min.css');
        Requirements::javascript('themes/openstack/javascript/jquery-ajax-loader.js');
        Requirements::javascript('themes/openstack/bower_assets/chosen/chosen.jquery.min.js');
        Requirements::javascript('themes/openstack/bower_assets/jquery-validate/dist/jquery.validate.min.js');
        Requirements::javascript('themes/openstack/bower_assets/jquery-validate/dist/additional-methods.min.js');
        Requirements::javascript('themes/openstack/javascript/urlfragment.jquery.js');
        Requirements::javascript('themes/openstack/bower_assets/pure-templates/libs/pure.min.js');
        Requirements::javascript('themes/openstack/bower_assets/jquery-cookie/jquery.cookie.js');

        // browser detection
        Requirements::javascript('themes/openstack/bower_assets/bowser/src/bowser.js');
        Requirements::javascript('themes/openstack/bower_assets/sweetalert2/dist/sweetalert2.min.js');
        Requirements::javascript('themes/openstack/javascript/jquery.serialize.js');
        Requirements::javascript('themes/openstack/javascript/jquery.cleanform.js');
        Requirements::javascript('themes/openstack/bower_assets/urijs/src/URI.min.js');
        Requirements::javascript('themes/openstack/bower_assets/urijs/src/URI.fragmentQuery.js');
        if($this->EnableMobileSupport) {
            Requirements::javascript('summit/javascript/schedule/install_mobile_app.js');
        }
        Requirements::javascript('summit/javascript/forms/rsvp.form.js');
    }

    const EventShareByEmailTokenKey = 'SummitAppEventPageShareEmail.Token';
    const EventShareByEmailCountKey = 'SummitAppEventPageShareEmail.Count';

    /**
     * @param SS_HTTPRequest $request
     * @return HTMLText|SS_HTTPResponse|void
     */
    public function ViewEvent(SS_HTTPRequest $request)
    {
        $event     = $this->getSummitEntity($request);
        $summit_id = $event->SummitID;

        if (is_null($event) || !$event->isPublished() || !ScheduleManager::allowToSee($event)) {
            return $this->httpError(404, 'Sorry that event could not be found');
        }

        // only send meta tags
        if($request->getHeader("Prefer-Html-Meta-Tags")){
            return $this->buildOnlyMetaTagsResponse($event->MetaTags());
        }

        Requirements::block("summit/css/schedule-grid.css");
        Requirements::block("summit/css/schedule-grid.css");
        Requirements::css("summit/css/summitapp-event.css");
        Requirements::javascript("summit/javascript/schedule/event-detail-page.js");


        //JS libraries for feedback form and list
        Requirements::javascript('marketplace/code/ui/frontend/js/star-rating.min.js');
        Requirements::css("marketplace/code/ui/frontend/css/star-rating.min.css");

        $token = Session::get(self::EventShareByEmailTokenKey);

        if (!$token) {
            $token = md5(uniqid(rand(), TRUE));
            Session::set(self::EventShareByEmailTokenKey, $token);
            Session::set(self::EventShareByEmailCountKey, 0);
        }

        $back_url           = $request->requestVar('BackURL') ;
        if(!Director::is_site_url($back_url) || empty($back_url)) {
            $start_date = new \DateTime($event->getStartDate());
            $main_schedule_page = SummitAppSchedPage::get()->filter("SummitID", $summit_id)->first();
            $back_url = $main_schedule_page->getAbsoluteLiveLink(false);
            $back_url .= sprintf("#day=%s-%s-%s&eventid=%s", $start_date->format('Y'), $start_date->format('m'), $start_date->format('d'), $event->ID);
        }

        return $this->renderWith(
            array('SummitAppEventPage', 'SummitPage', 'Page'),
            array(
                'Event'     => $event,
                'BackURL'   => $back_url,
                'Token'     => $token
            ));
    }

    /**
     * @param SS_HTTPRequest $request
     * @return HTMLText|SS_HTTPResponse|void
     */
    public function ViewEventRSVP(SS_HTTPRequest $request){

        Requirements::block("summit/css/schedule-grid.css");
        Requirements::css("summit/css/summitapp-event.css");
        Requirements::css("summit/css/summitapp-event-rsvp.css");
        Requirements::javascript("summit/javascript/schedule/event-detail-page.js");
        $mobile_detect = new Mobile_Detect();
        $event         = $this->getSummitEntity($request);
        $summit_id     = $event->SummitID;

        if(is_null($event) ||
            !$event->isPublished() ||
            !ScheduleManager::allowToSee($event) ||
            !$event->hasRSVPTemplate()) {
            return $this->httpError(404, 'Sorry that event could not be found');
        }

        if (!Member::currentUser()){
            return $this->redirect('Security/login?BackURL='.$event->getRSVPURL(false));
        }

        if(!Member::currentUser()->isAttendee($event->SummitID)){
            return $this->redirect('profile/attendeeInfoRegistration');
        }

        $has_rsvp_submission = Member::currentUser()->getSummitAttendee($event->SummitID)->hasRSVPSubmission($event->getIdentifier());

        if(!$mobile_detect->isMobile() && $has_rsvp_submission)
        {
            return $this->redirect($event->getLink('show'));
        }

        $token = Session::get(self::EventShareByEmailTokenKey);

        if (!$token) {
            $token = md5(uniqid(rand(), TRUE));
            Session::set(self::EventShareByEmailTokenKey, $token);
            Session::set(self::EventShareByEmailCountKey, 0);
        }

        if(Director::is_ajax()) {
            return $this->renderWith(
                array('SummitAppEventPage_RSVP_AjaxForm'),
                array(
                    'Event'     => $event,
                    'Token'     => $token
                ));
        }

        $back_url           = $request->requestVar('BackURL') ;

        if(!Director::is_site_url($back_url) || empty($back_url)) {
            $start_date = new \DateTime($event->getStartDate());
            $main_schedule_page = SummitAppSchedPage::get()->filter("SummitID", $summit_id)->first();
            $back_url = $main_schedule_page->getAbsoluteLiveLink(false);
            $back_url .= sprintf("#day=%s-%s-%s&eventid=%s", $start_date->format('Y'), $start_date->format('m'), $start_date->format('d'), $event->ID);
        }

        return $this->renderWith(
            array('SummitAppEventPage_RSVP', 'SummitPage', 'Page'),
            array(
                'BackURL'        => $back_url,
                'Event'          => $event,
                'Token'          => $token,
                'HasRSVPAlready' => $has_rsvp_submission
            ));
    }

    public function getProfileAttendeeRegistrationLink(){
        $page = EditProfilePage::get()->first();
        if(!$page) return '#';
        return $page->Link("attendeeInfoRegistration");
    }

    /**
     * @param SS_HTTPRequest $request
     * @return HTMLText|void
     */
    public function eventDetails(SS_HTTPRequest $request)
    {
        if(!Director::is_ajax())
            return $this->httpError(404, 'Sorry that event could not be found');

        $event_id = intval($request->param('EVENT_ID'));
        $event    = $this->event_repository->getById($event_id);
        if (is_null($event) || !$event->isPublished() || !ScheduleManager::allowToSee($event)) {
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
        $event          = $this->event_repository->getById($event_id);
        $rsvp_template  = $event->RSVPTemplate();
        $attendee       = Member::currentUser()->getSummitAttendee($this->Summit()->ID);
        $rsvp           = null;
        if ($attendee) {
            $rsvp = $this->rsvp_repository->getByEventAndAttendee($event_id, $attendee->ID);
        }

        $builder        = new RSVPTemplateUIBuilder();
        return $builder->build($rsvp_template, $rsvp, $event);
    }

    /**
     * @param SS_HTTPRequest $request
     * @return HTMLText|SS_HTTPResponse|void
     */
    public function ViewMySchedule(SS_HTTPRequest $request)
    {
        $member    = Member::currentUser();
        $goback    = $request->getHeader('Referer') && trim($request->getHeader('Referer'),'/') == trim(Director::absoluteURL($this->Link()),'/')? '1':'';

        if (is_null($this->Summit()))
            return $this->httpError(404, 'Sorry, summit not found');

        if(is_null($member)){
            return $this->redirect('Security/login?BackURL='.$this->Link('mine'));
        }

        if(!$member->isAttendee($this->Summit()->ID)){
            return $this->redirect($this->Link());
        }

        $my_schedule = $member->getSummitAttendee($this->Summit()->ID)->Schedule()->sort(array('StartDate'=>'ASC','Location.Name' => 'ASC'));

        return $this->renderWith(
            ['SummitAppMySchedulePage', 'SummitPage', 'Page'],
            [
                'Schedule' => $my_schedule,
                'Summit'   => $this->Summit(),
                'goback'   => $goback
            ]);
    }

    public function ViewFullSchedule(SS_HTTPRequest $request)
    {
        $goback   = $request->getHeader('Referer') && trim($request->getHeader('Referer'),'/') == trim(Director::absoluteURL($this->Link()),'/')? '1':'';

        if (is_null($this->Summit())) return $this->httpError(404, 'Sorry, summit not found');

        $schedule = $this->Summit()->getSchedule();
        Requirements::javascript("summit/javascript/schedule/full-schedule-page.js");

        return $this->renderWith(
            ['SummitAppFullSchedulePage', 'SummitPage', 'Page'],
            [
                'Schedule' => $schedule,
                'Summit'   => $this->Summit(),
                'goback'   => $goback
            ]);
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
            $html2pdf->setTestTdInOnePage(false);
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
        $sort      = $this->getRequest()->getVar('sort') ? $this->getRequest()->getVar('sort') : 'day';
        $show_desc = $this->getRequest()->getVar('show_desc') ? $this->getRequest()->getVar('show_desc') : false;
        $base      = Director::protocolAndHost();

        if (is_null($this->Summit())) return $this->httpError(404, 'Sorry, summit not found');

        $schedule  = $this->Summit()->getSchedule();
        $events    = new ArrayList();
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
            $html2pdf->setTestTdInOnePage(false);
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
            return $this->buildOnlyMetaTagsResponse($speaker->MetaTags());
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
     * @param string $meta_tags
     * @return SS_HTTPResponse
     */
    private function buildOnlyMetaTagsResponse($meta_tags){
        $response = new SS_HTTPResponse();
        $response->setStatusCode(200);
        $html = <<< APP_LINKS
               <html>
                <head>
                    {$meta_tags}
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

        $term = urldecode($term);

        $speakers = $this->speaker_repository->searchBySummitAndTerm($this->Summit(), $term);
        $events   = $this->event_repository->searchBySummitAndTerm($this->Summit(), $term);

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
        $request = $this->getRequest();
        $action  = $request->param("Action");
        $entity  = $this->getSummitEntity($request);

        if(!is_null($entity)){
            return $entity->MetaTags();
        }
        $tags = parent::MetaTags();
        // default one
        $url_path = "schedule";
        if(!empty($action) && $action == 'global-search'){
            $term = Convert::raw2sql($request->requestVar('t'));
            $url_path = "search";
            if(!empty($term)){
                $url_path .= "/".urlencode($term);
            }
        }
        // IOS
        $tags .= AppLinkIOSMetadataBuilder::buildAppLinksMetaTags($tags, $url_path);
        // Android
        $tags .= AppLinkIAndroidMetadataBuilder::buildAppLinksMetaTags($tags, $url_path);
        return $tags;
    }

    public function index(SS_HTTPRequest $request){
        // only send meta tags
        if($request->getHeader("Prefer-Html-Meta-Tags")){
            return $this->buildOnlyMetaTagsResponse($this->MetaTags());
        }
        Requirements::javascript("summit/javascript/schedule/event-detail-page.js");

        return $this->getViewer('index')->process($this);
    }

    /**
     * @return string
     */
    public function getGoogleCalendarClientID() {
        return GAPI_CLIENT;
    }
}