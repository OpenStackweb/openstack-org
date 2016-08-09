<?php
/**
 * Copyright 2014 Openstack Foundation
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 **/
class VideoLink extends DataObject {

    private static $db = array(
        'YoutubeID' => 'Text',
        'Caption' => 'Text',
        'SortOrder' => 'Int'
    );

    private static $has_one = array(
        'Thumbnail' => 'Image',
    );

	private static $default_sort = 'SortOrder';

    private static $summary_fields = array
    (
        'YoutubeID'  => 'YoutubeID',
        'Caption'    => 'Caption',
    );

    function getCMSFields(){

	    $fields = new FieldList;

        $image = new CustomUploadField('Thumbnail','Thumbnail');
	    $image->setFolderName('assets/vid_thumbs');
	    $image->setAllowedFileCategories('image');

        $image_validator = new Upload_Validator();
        $image_validator->setAllowedExtensions(array('jpg','png','jpeg'));
        $image->setValidator($image_validator);

	    $fields->push(new TextField('YoutubeID','YouTube ID'));
	    $fields->push(new TextField('Caption'));
	    $fields->push($image);

        return $fields;
    }

    function getValidator()	{
        $validator= new FileRequiredFields(array('Url'));
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

    public function getThumbnailUrl() {
        if ($this->Thumbnail()->Exists()) {
            return $this->Thumbnail()->getUrl();
        }

        return 'http://img.youtube.com/vi/'.$this->YoutubeID.'/0.jpg';
    }

}