<?php

class SummitSchedPageMain extends SummitPage {
    private static $db = array (
        'SchedEmbedURL' => 'Text'
    );
        
    public function getCMSFields() {
        $fields = parent::getCMSFields();
                
        $fields->addFieldToTab('Root.Main', new TextField('SchedEmbedURL','Enter The Sched Embed URL'), 'Content');
        
        return $fields;    

    }    
    
    
}


class SummitSchedPageMain_Controller extends SummitPage_Controller {

    public function init() {
        
        parent::init();
        Requirements::javascript("summit/javascript/schedule.js");
                
	}
    
	
}