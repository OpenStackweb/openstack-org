<?php
	class LegalDocumentPage extends Page {
		static $db = array(
		);
		static $has_one = array(
			'LegalDocumentFile' => 'File'
	     );

	 	function getCMSFields() {
	    	$fields = parent::getCMSFields();

	    	$AttachmentField = new FileIFrameField ('LegalDocumentFile','Attach a PDF or DOC file');
	    	$AttachmentField->allowedExtensions = array('doc','pdf');

	    	$fields->addFieldToTab('Root.Main', $AttachmentField);
	    	    	    		    
	    	return $fields;
	 	}

	}

	class LegalDocumentPage_Controller extends Page_Controller {
		function init() {
			parent::init();
		}
	}