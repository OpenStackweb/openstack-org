<?php

class UserStoryHolder extends Page {

	static $db = array(
	);
	static $has_one = array(
	);
	
	static $allowed_children = array('UserStory');

	function getCMSFields(){
		
		$fields = parent::getCMSFields();

		$config = GridFieldConfig_RecordEditor::create();
		$config->getComponentByType('GridFieldDataColumns')->setDisplayFields(
			array('IndustryName' => 'IndustryName', 'Active' => 'Active')
		);
		$industries =  new GridField('UserStoriesIndustry', 'User Stories Industry',UserStoriesIndustry::get(), $config);
		$fields->addFieldsToTab('Root.Industries',$industries);

		$config = GridFieldConfig_RecordEditor::create();
		$config->getComponentByType('GridFieldDataColumns')->setDisplayFields(
			array('UserStory.Title' => 'UserStory', 'UserStoriesIndustry.IndustryName' => 'Industry')
		);
		$Featured =  new GridField('UserStoriesFeatured', 'User Stories Featured',UserStoriesFeatured::get(),$config );
		$fields->addFieldsToTab('Root.FeaturedStories',$Featured);

		$config = GridFieldConfig_RecordEditor::create();
		$config->getComponentByType('GridFieldDataColumns')->setDisplayFields(
			array('LinkName' => 'LinkName', 'UserStory.Title' => 'UserStory')
		);
		$links =  new GridField('UserStoriesLink', 'User Stories Link',UserStoriesLink::get(), $config );
		$fields->addFieldsToTab('Root.Links',$links);

		$config = GridFieldConfig_RecordEditor::create();
		$config->getComponentByType('GridFieldDataColumns')->setDisplayFields(
			array('Topic' => 'Topic')
		);
		$topics =  new GridField('UserStoriesTopics', 'User Stories Topics',UserStoriesTopics::get(),$config );
		$fields->addFieldsToTab('Root.Topics',$topics);

		$config = GridFieldConfig_RecordEditor::create();
		$config->getComponentByType('GridFieldDataColumns')->setDisplayFields(
			array('UserStoriesTopics.Topic' => 'Topic', 'LabelTitle' => 'Title')
		);
		$TopicsFeatured =  new GridField('UserStoriesTopicsFeatured', 'User Stories Topics Featured',UserStoriesTopicsFeatured::get(), $config );
		$fields->addFieldsToTab('Root.FeaturedOnSlider',$TopicsFeatured);

		$config = GridFieldConfig_RecordEditor::create();
		$config->getComponentByType('GridFieldDataColumns')->setDisplayFields(
			array('Type' => 'Type', 'Content' => 'Content')
		);
		$slides =  new GridField('UserStoriesSlides', 'User Stories Slides', UserStoriesSlides::get(), $config );
		$fields->addFieldsToTab('Root.Slides',$slides);

		return $fields;
	}

	public function stageChildren($showAll = false) 
    {
        if(Controller::curr()->getRequest()->param("Action") == "getfilteredsubtree"
        && (!empty($_REQUEST['SiteTreeSearchTerm']) || !empty($_REQUEST['SiteTreeFilterDate'])))
        {            
            $staged = UserStory::get()->filter(
	            array('ShowInAdmin' => true,
	                   'ParentID'   => $this->owner->ID)
            );
            if(!$staged){
            	$staged = new DataList('UserStory');
            }
            $this->owner->extend("augmentStageChildren", $staged, $showAll);
            return $staged;
        }
        
        else
        {
	        $set = UserStory::get()->filter(
		        array('ShowInAdmin' => true,
			        'ParentID'   => $this->owner->ID)
	        );
            if(!$set){
            	return array();
            }
            return $set;    
        }
    }

}
  
class UserStoryHolder_Controller extends Page_Controller {
	
	function Industries() {
		return UserStoriesIndustry::get()->filter('Active',1);
	}

	function Slider(){
		return UserStoriesSlides::get();
	}
}