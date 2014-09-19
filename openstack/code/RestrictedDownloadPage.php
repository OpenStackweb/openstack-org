<?php

/**
 * Defines the RestrictedDownload page type
 */
class RestrictedDownloadPage extends Page
{
	static $db = array(
		'GuidelinesLogoLink' => 'Text'
	);
	static $has_one = array();

	function getCMSFields()
	{
		$fields = parent::getCMSFields();
		$fields->addFieldToTab('Root.Main', new TextField('GuidelinesLogoLink', 'Image URL for the guidelines logo in upper right corner'), 'Content');

		return $fields;
	}
}

class RestrictedDownloadPage_Controller extends Page_Controller
{

	function init()
	{
		parent::init();

		$ParentURL = $this->Parent()->Link();

		//check to see if they've completed an approval form
		if (!Session::get('LogoSignoffCompleted')) {
			$this->redirect($ParentURL);
		}

	}

	function BrandingMenu()
	{
		return TRUE;
	}

}