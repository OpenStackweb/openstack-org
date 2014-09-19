<?php
class SectionLink extends DataObject {

	private static $db = array(
			'SortOrder' => 'Int',
			'Link' => 'Text'
	);
	
	private static $has_one = array(
			'Image' => 'Image',
			'MarketingPage' => 'MarketingPage'
	);

	private static $default_sort = 'SortOrder';
	
	function getCMSFields(){

		$fields = new FieldList;

		$image  = new CustomUploadField('Image','Image');
		$image->setFolderName('marketing/section_link');
		$image->setAllowedFileCategories('image');
	
		$image_validator = new Upload_Validator();
		$image_validator->setAllowedExtensions(array('jpg','png','jpeg'));
		$image->setValidator($image_validator);

		$fields->push(new TextField('Link'));
		$fields->push($image);

		return $fields;

	}
	
	function getValidator()	{
	    $validator= new FileRequiredFields(array('Link','SortOrder'));
        $validator->setRequiredFileFields(array("Image"));
        return $validator;
	}
	
	public function SmallPreview(){
		$img = $this->Image();
		if($img->exists()){
			return $img->SetRatioSize('150','75');
		}
		return 'n/a';
	}
	
	public function getPreview(){
		$img = $this->Image();
		if($img->exists()){
			return $img->SetRatioSize('300','150');
		}
		return 'n/a';
	}
	
	public function getFileName(){
		$img = $this->Image();
		if($img->exists()){
			return $img->Filename;
		}
		return 'n/a';
	}
	
	public function getSortOrderItem(){
		return $this->SortOrder+1;
	}
}