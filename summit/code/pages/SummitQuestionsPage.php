<?php

class SummitQuestionsPage extends SummitPage {
    
	private static $has_many = array (
		'Questions' => 'SummitQuestion',
        'Categories' => 'SummitQuestionCategory'
	);    
    
    public function getCMSFields() {
        $fields = parent::getCMSFields();
        if($this->ID) {
            
            // Summit Questions
            $questionFields = singleton('SummitQuestion')->getCMSFields();
            $config = GridFieldConfig_RelationEditor::create();
            $config->getComponentByType('GridFieldDetailForm')->setFields($questionFields);
            $config->addComponent(new GridFieldSortableRows('Order'));
            $gridField = new GridField('Questions', 'Questions', $this->Questions(), $config);
            $fields->addFieldToTab('Root.Questions',$gridField);
            
            // Summit Question Categories
            $categoryFields = singleton('SummitQuestionCategory')->getCMSFields();
            $config = GridFieldConfig_RelationEditor::create();
            $config->getComponentByType('GridFieldDetailForm')->setFields($categoryFields);
            $gridField = new GridField('Categories', 'Categories', $this->Categories(), $config);
            $fields->addFieldToTab('Root.QuestionCategories',$gridField);        
            
        }
        return $fields;    

    }    
    
    public function getGroupedQuestions() {
        return GroupedList::create($this->Questions()->sort('Order'));
    }    
    

}


class SummitQuestionsPage_Controller extends SummitPage_Controller {
    
	public function init() {
		parent::init();
		Requirements::javascript("summit/javascript/jquery-livesearch.js");		         
        Requirements::javascript("summit/javascript/faq.js");
	}    

    public function getCategorySlug($CategoryName) {
        $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $CategoryName);
        $clean = strtolower(trim($clean, '-'));
        $clean = preg_replace("/[\/_|+ -]+/", '-', $clean);
        return $clean;
    }    

}