<?php

class SummitSchedPage extends SummitPage {
    private static $db = array (
        'SchedEmbedURL' => 'Text'
    );
        
    public function getCMSFields() {
        $fields = parent::getCMSFields();
                
        $fields->addFieldToTab('Root.Main', new TextField('SchedEmbedURL','Enter The Sched Embed URL'), 'Content');
        
        return $fields;    

    }    
    
    
}


class SummitSchedPage_Controller extends SummitPage_Controller {

    public function init() {
        
        $this->top_section = 'full';
        parent::init();
                
	}
    
	
}