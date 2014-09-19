<?php
class CaseStudy extends DataObject{

	private static $db = array(
		'Name' => 'Text',
		'Tagline' => 'Text',
		'Link' => 'Text',
		'SortOrder' => 'Int'
	);

	private static $default_sort = 'SortOrder';

	private static $has_one = array(
		'Thumbnail' => 'Image',
		'MarketingPage' => 'MarketingPage'
	);
	
	function getCMSFields(){
	
		$image = new CustomUploadField('Thumbnail','Thumbnail');
		//save to path marketing/case_study
		$image->setFolderName('marketing/case_study');
		$image->setAllowedFileCategories('image');

		$image_validator = new Upload_Validator();
		$image_validator->setAllowedExtensions(array('jpg','png','jpeg'));
		$image->setValidator($image_validator);

		return new FieldList(
				new TextField('Name'),
				new TextField('Tagline'),
				new TextField('Link'),
				$image
		);
	}
	
	function getValidator()	{
		$validator= new FileRequiredFields(array('Name','Tagline','Link'));
        $validator->setRequiredFileFields(array("Thumbnail"));
        return $validator;
	}
	
	public function getPreview(){
		$img = $this->Thumbnail();
		if($img->exists()){
			return $img->SetRatioSize('60','60');
		}
		return 'n/a';
	}
	

	public function getSmallPreview(){
		$img = $this->Thumbnail();
		if($img->exists()){
			return $img->SetRatioSize('30','30');
		}
		return 'n/a';
	}
	
}