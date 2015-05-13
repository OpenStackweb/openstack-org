<?php

class SummitUpdatesPage extends SummitPage {
    
	private static $has_many = array (
		'SummitUpdates'  => 'SummitUpdate',
        'ImportantDates' => 'SummitActivityDate'
	);
    
    public function getCMSFields() {
        $fields = parent::getCMSFields();
        if($this->ID) {
            
            // Summit Updates
            $updateFields = singleton('SummitUpdate')->getCMSFields();
            $config = GridFieldConfig_RelationEditor::create();
            $config->addComponent(new GridFieldSortableRows('Order'));
            $gridField = new GridField('Updates', 'Updates', $this->SummitUpdates(), $config);
            $fields->addFieldToTab('Root.Updates',$gridField);
            
            // Summit Important Dates
            $dateFields = singleton('SummitActivityDate')->getCMSFields();
            $config = GridFieldConfig_RelationEditor::create();
            $config->getComponentByType('GridFieldDetailForm')->setFields($dateFields);
            $gridField = new GridField('ImportantDates', 'Important Dates', $this->ImportantDates(), $config);
            $fields->addFieldToTab('Root.ImportantDates',$gridField);        
            
        }
        return $fields;    

    }
}


class SummitUpdatesPage_Controller extends SummitPage_Controller {
    
	public function init() {
        $this->top_section = 'full';
        parent::init();
	}        

    function sortedSummitUpdates() {
        return $this->SummitUpdates()->sort('Order');
    }
    
}