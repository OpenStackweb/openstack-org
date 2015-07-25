<?php

class SummitAppSchedPage extends SummitPage {

}


class SummitAppSchedPage_Controller extends SummitPage_Controller {

    static $allowed_actions = array(
        'ViewEvent',
    );

    static $url_handlers = array(
        'event/$EVENT_ID/$EVENT_TITLE'   => 'ViewEvent',
    );

    public function init() {
        
        $this->top_section = 'full';
        $this->event_repository = new SapphireSummitEventRepository();
        parent::init();

        Requirements::javascript('themes/openstack/javascript/pure.min.js');
        Requirements::javascript("summit/javascript/summitapp-schedule.js");
        Requirements::css("summit/css/summitapp-schedule.css");
	}

    public function ViewEvent() {
        $event_id = intval($this->request->param('EVENT_ID'));
        $this->event_id = $event_id;
        $event = $this->event_repository->getById($event_id);

        if (!isset($event)) {
            return $this->httpError(404, 'Sorry that article could not be found');
        }

        return $this->renderWith(array('SummitAppEventPage','SummitPage','Page'), array('Event' => $event) );
    }

    public function getFeedbackForm() {
        Requirements::javascript(Director::protocol()."ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js");
        Requirements::javascript(Director::protocol()."ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/additional-methods.min.js");
        Requirements::javascript("marketplace/code/ui/frontend/js/star-rating.min.js");
        Requirements::javascript("summit/javascript/summitapp-feedbackform.js");

/*        Requirements::combine_files('marketplace_review_form.js', array(
                "themes/openstack/javascript/jquery.validate.custom.methods.js",
                "marketplace/code/ui/frontend/js/star-rating.min.js",
                "marketplace/code/ui/frontend/js/marketplace.review.js"
            )
        );*/

        Requirements::css("marketplace/code/ui/frontend/css/star-rating.min.css");

        $form = new SummitEventFeedbackForm($this, 'SummitEventFeedbackForm');
        return $form;
    }
}