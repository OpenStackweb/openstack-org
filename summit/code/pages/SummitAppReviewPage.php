<?php

/**
 * Class SummitAppReviewPage
 */
class SummitAppReviewPage extends SummitPage
{

}

/**
 * Class SummitAppReviewPage_Controller
 */
class SummitAppReviewPage_Controller extends SummitPage_Controller
{

    static $allowed_actions = array(
    );

    static $url_handlers = array
    (
    );

    public function init()
    {

        $this->top_section = 'short'; //or full
        parent::init();

        if(!Member::currentUser())
            return OpenStackIdCommon::doLogin();

        Requirements::javascript("summit/bower_components/sweetalert/lib/sweet-alert.js");
        Requirements::css("summit/bower_components/sweetalert/lib/sweet-alert.css");
        //Requirements::javascript('//tinymce.cachefly.net/4.3/tinymce.min.js');
        Requirements::javascript('summit/javascript/summitapp-review.js');
        Requirements::javascript('marketplace/code/ui/frontend/js/star-rating.min.js');
        Requirements::css("marketplace/code/ui/frontend/css/star-rating.min.css");

    }

    public function index(){

        $member    = Member::currentUser();

        if (is_null($this->Summit())) return $this->httpError(404, 'Sorry, summit not found');
        if(is_null($member)) return $this->httpError(401, 'You need to login to access your schedule.');

        $is_attendee = $member->isAttendee($this->Summit()->ID);
        $my_schedule = '';

        if ($is_attendee)
            $my_schedule = $member->getSummitAttendee($this->Summit()->ID)->Schedule()->sort(array('StartDate'=>'ASC','Location.Name' => 'ASC'));

        return $this->renderWith(
            array('SummitAppReviewPage', 'SummitPage', 'Page'),
            array(
                'Schedule' => $my_schedule,
                'Summit'   => $this->Summit()
            )
        );
    }


}