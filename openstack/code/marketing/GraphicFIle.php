<?php
class GraphicFile extends DataObject{
	
	static $db = array(
		'Name' => 'Text',
		'SortOrder' => 'Int'
	);
	
	
	static $has_one = array(
		'Thumbnail' => 'Image',
		'Attachment' => 'File',	
		'Graphic' => 'Graphic'			
	);
	
	function getCMSFields(){
		$attach = new CustomUploadField('Attachment','File');
		$attach->setFolderName('marketing/graphics');
		
		$image = new CustomUploadField('Thumbnail','Thumbnail');
		$image->setFolderName('marketing/graphics');
		$image->setAllowedFileCategories('image');

		$image_validator = new Upload_Validator();
		$image_validator->setAllowedExtensions(array('jpg','png','jpeg'));
		$image->setValidator($image_validator);

		return new FieldList(
			new TextField('Name'),
			$image,
			$attach
		);
	}
	
	function getValidator()
	{
        $validator= new FileRequiredFields(array('Name'));
        $validator->setRequiredFileFields(array('Thumbnail','Attachment'));
        return $validator;
	}
	
	public function getPreview(){
		$img = $this->Thumbnail();
		if($img->exists()){
			return $img->SetRatioSize('100','100');
		}
		return 'n/a';
	}
	
	public function getSmallPreview() {
		$img = $this->Thumbnail();
		if ($img->exists()) {
			return $img->SetRatioSize('20', '20');
		}
		return 'n/a';
	}
	
	public function ExistAttachment(){
		return $this->Attachment()->Exists();
	}
	
	public function ExistPreview(){
		return $this->Thumbnail()->Exists();
	}
	
	public function getURLPreview(){
		$img = $this->Thumbnail();
		if($img->exists()){
			return $img->getURL();
		}
		return '#';
	}
}