<?php
	class CommunityPage extends Page {
		static $db = array(
		      'TopSection' => 'HTMLText'
		);
		static $has_one = array(
	     );

	 	function getCMSFields() {
	    	$fields = parent::getCMSFields();
	    	$fields->addFieldToTab('Root.Main', new HTMLEditorField('TopSection','Top Section'), 'Content');
	    	$fields->renameField("Content", "Middle Area");

	    	return $fields;
	 	}  

	}

	class CommunityPage_Controller extends Page_Controller {

		function init() {
			parent::init();
       	}
		
		function DeveloperActivityFeed(){ 
	   		$data = file_get_contents('http://www.openstack.org/feeds/developer-activity.php');
	   		return $data;
		}
		
		function PullFeed(){ 
			   		$data = file_get_contents('http://www.openstack.org/simplepie/flickr.php'); 
			   		return $data;
		}
		
		function CompanyCount() {
			$Count = Company::get()->filter('DisplayOnSite',true)->count();
			// Round down to nearest multiple of 5
			$Count = round(($Count-2.5)/5)*5;
			return $Count;
		}
				
	}