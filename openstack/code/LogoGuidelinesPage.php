<?php
/**
 * Defines the logo permission form page type
 */
class LogoGuidelinesPage extends Page {

	static $db = array(
    	'Preamble' => 'HTMLText',
    	'TrademarkURL' => 'Text'
		);
		
	static $has_many = array(
	);
		
	public function getCMSFields()
	{
	
		$fields = parent::getCMSFields();
						
    	$fields->addFieldToTab('Root.Main', new TextField('TrademarkURL','Path to page 2, the trademark agreement (URL)'), 'Content');
    	$fields->addFieldToTab('Root.Main', new HTMLEditorField('Preamble','Welcome Message'), 'Content');
		
		$fields->renameField("Content", "Logo Guidelines");
		
		return $fields;
	}	
	
}
 
class LogoGuidelinesPage_Controller extends Page_Controller {

	// enables the form to be submitted
	static $allowed_actions = array(
		'GuidelinesForm'
	);
	
	function GuidelinesForm() {
		// Create fields 
		$fields = new FieldList(
		new HiddenField('ReadGuidelines',TRUE)
		);
		
		// Create action
		$actions = new FieldList(
		new FormAction('doReadGuidelines', 'AGREE & CONTINUE')
		);
		
		// Create Validators
		
		// Form(controller, form name, fields, actions, validator)
		return new Form($this, 'GuidelinesForm', $fields, $actions);
	}
	
	// called when the form is submitted (see 'form action' above)
	function doReadGuidelines($data, $form) {
	    Session::set('ReadGuidelines', true);
		$this->redirect($this->TrademarkURL);
	}
	
	
	function init() {
		parent::init();

		Requirements::customCSS("fieldset {display:none}");

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
	
	function BrandingMenu() {
		return TRUE;
	}
}