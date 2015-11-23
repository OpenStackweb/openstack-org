<?php

class SummitAppSchedPage extends SummitPage {

}


class SummitAppSchedPage_Controller extends SummitPage_Controller {

    static $allowed_actions = array(
        'ViewEvent',
        'ViewSpeakerProfile',
    );

    static $url_handlers = array(
        'event/$EVENT_ID/$EVENT_TITLE'   => 'ViewEvent',
        'speaker/$SPEAKER_ID'            => 'ViewSpeakerProfile',
    );

    public function init() {
        
        $this->top_section = 'full';
        $this->event_repository = new SapphireSummitEventRepository();
        $this->speaker_repository = new SapphirePresentationSpeakerRepository();

        parent::init();
        Requirements::css('themes/openstack/bower_assets/jquery-loading/dist/jquery.loading.min.css');
        Requirements::css('themes/openstack/bower_assets/chosen/chosen.min.css');
        Requirements::css("summit/css/schedule-grid.css");
        Requirements::javascript('themes/openstack/javascript/jquery-ajax-loader.js');
        Requirements::javascript('themes/openstack/bower_assets/chosen/chosen.jquery.min.js');
   }

    public function ViewEvent() {
        $event_id = intval($this->request->param('EVENT_ID'));
        $this->event_id = $event_id;
        $event = $this->event_repository->getById($event_id);

        if (!isset($event)) {
            return $this->httpError(404, 'Sorry that article could not be found');
        }

        Requirements::block("summit/css/schedule-grid.css");
        Requirements::css("summit/css/summitapp-event.css");

        return $this->renderWith(array('SummitAppEventPage','SummitPage','Page'), array('Event' => $event) );
    }

    public function ViewSpeakerProfile() {
        $speaker_id = intval($this->request->param('SPEAKER_ID'));
        $speaker = $this->speaker_repository->getById($speaker_id);

        if (!isset($speaker)) {
            return $this->httpError(404, 'Sorry that speaker could not be found');
        }

        Requirements::block("summit/css/schedule-grid.css");
        Requirements::css("summit/css/summitapp-speaker.css");

        return $this->renderWith(array('SummitAppSpeakerPage','SummitPage','Page'), array('Speaker' => $speaker) );
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


    public function getCurrentSummitEventsBy1stDate()
    {
        $summit = $this->Summit();
        return new ArrayList($summit->getSchedule($summit->getBeginDate()));
    }

    public function isEventOnMySchedule($event_id)
    {
       return SummitAppScheduleApi::isEventOnMySchedule($event_id, $this->Summit());
    }
}