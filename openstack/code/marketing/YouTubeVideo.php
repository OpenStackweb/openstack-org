<?php

class YouTubeVideo extends DataObject {

    private static $db = array(
        'Url' => 'Text',
        'SortOrder' => 'Int'
    );

    private static $has_one = array(
        'MarketingPage' => 'MarketingPage',
        'Thumbnail' => 'Image',
    );

	private static $default_sort = 'SortOrder';

    function getCMSFields(){

	    $fields = new FieldList;

        $image = new CustomUploadField('Thumbnail','Thumbnail');
	    $image->setFolderName('marketing/youtube_vids_thumbs');
	    $image->setAllowedFileCategories('image');

        $image_validator = new Upload_Validator();
        $image_validator->setAllowedExtensions(array('jpg','png','jpeg'));
        $image->setValidator($image_validator);

	    $fields->push(new TextField('Url'));
	    $fields->push($image);

    }

    function getValidator()	{
        $validator= new FileRequiredFields(array('Url'));
        $validator->setRequiredFileFields(array("Thumbnail"));
        return $validator;
    }

    public function getFileName(){
        $img = $this->Thumbnail();
        if($img->exists()){
            return $img->Filename;
        }
        return 'n/a';
    }

    public function getSmallPreview(){
        $img = $this->Thumbnail();
        if($img->exists()){
            return $img->SetRatioSize('150','75');
        }
        return 'n/a';
    }

    public function getPreview(){
        $img = $this->Thumbnail();
        if($img->exists()){
            return $img->SetRatioSize('300','169');
        }
        return 'n/a';
    }
}