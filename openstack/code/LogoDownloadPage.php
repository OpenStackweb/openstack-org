<?php

/**
 * Defines the LogoDownloads page type
 */
class LogoDownloadPage extends Page
{
	static $db = array();
	static $has_one = array();

	function getCMSFields()
	{
		$fields = parent::getCMSFields();

		return $fields;
	}
}

class LogoDownloadPage_Controller extends Page_Controller
{

	function init()
	{
		parent::init();

		$ParentURL = $this->Parent()->Link();

		//check to see if they've completed an approval form
		if (!Session::get('ReadGuidelines')) {
			$this->redirect($ParentURL);
		}

		Requirements::javascript("http://assets.zendesk.com/external/zenbox/v2.5/zenbox.js");
		Requirements::css("http://assets.zendesk.com/external/zenbox/v2.5/zenbox.css");
		Requirements::customScript('
			 if (typeof(Zenbox) !== "undefined") {
			    Zenbox.init({
			      dropboxID:   "20115046",
			      url:         "https://openstack.zendesk.com",
			      tabID:       "Ask Us",
			      tabColor:    "black",
			      tabPosition: "Right"
			    });
			  }

		');


	}

	function BrandingMenu()
	{
		return TRUE;
	}

}