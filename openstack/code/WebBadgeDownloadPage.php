<?php

/**
 * Defines the LogoDownloads page type
 */
class WebBadgeDownloadPage extends Page
{
	static $db = array();
	static $has_one = array();

	function getCMSFields()
	{
		$fields = parent::getCMSFields();

		return $fields;
	}
}

class WebBadgeDownloadPage_Controller extends Page_Controller
{

	function init()
	{
		parent::init();

		$ParentURL = $this->Parent()->Link();

		//check to see if they've completed an approval form
		if (!Session::get('ReadGuidelines')) {
			$this->redirect($ParentURL);
		}

	}

	function BrandingMenu()
	{
		return TRUE;
	}

}