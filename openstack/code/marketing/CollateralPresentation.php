<?php

class CollateralPresentation extends DataObject {

	private static $db = array(
		'Name' => 'Text',
		'SortOrder' => 'Int'
	);

	private static $default_sort = 'SortOrder';

	private static $has_one = array(
		'Attachment' => 'File',	
		'MarketingPage' => 'MarketingPage'
	);
	
	function getCMSFields(){
		$attach = new CustomUploadField('Attachment','File');
		$attach->setFolderName('marketing/presentations');
		$attach->setAllowedFileCategories('doc');
      	return new FieldList(new TextField('Name'),$attach);
	}
	
	function getValidator()	{
		$validator = new FileRequiredFields(array('Name'));
        $validator->setRequiredFileFields(array("Attachment"));
        return $validator;
	}
	
	public function getFileURL(){
		$file = $this->Attachment();
		if ($file->exists()) {
			return $file->getURL();
		}
	}
	
	public function getFileName(){
		$file = $this->Attachment();
		if ($file->exists()) {
			return $file->Name;
		}
		return 'n/a';
	}
	
	public function getIcon(){
		$icon = new Iconifier();
		return $icon->getIcon($this->Attachment()->Filename);
	}

}