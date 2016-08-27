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
class MarketingSoftware extends DataObject {

	private static $db = array(
		'Name'        => 'Varchar(255)',
		'Description' => 'HTMLText',
        'YoutubeID'   => 'Varchar(255)',
        'ReleaseLink' => 'Varchar(255)',
        'SortOrder'   => 'Int',
	);

	private  static $has_one = array(
        'ParentPage'   => 'MarketingPage',
		'Logo'         => 'BetterImage',
		'Presentation' => 'File',
	);

	private static $default_sort = 'SortOrder';


    function getCMSFields(){

        $fields = new FieldList;

        $fields->push(new TextField('Name','Name'));
        $fields->push($description = new HtmlEditorField('Description'));
        $description->setRows(5);
        $fields->push(new TextField('ReleaseLink','Release Link'));
        $fields->push(new TextField('SortOrder','Sort Order'));

        $fields->push(new TextField('YoutubeID','YouTube ID for video Link'));
        //$fields->merge($this->Video()->getCMSFields());

        $image = new CustomUploadField('Logo','Logo');
        $image->setFolderName('marketing');
        $image->setAllowedFileCategories('image');

        $image_validator = new Upload_Validator();
        $image_validator->setAllowedExtensions(array('jpg','png','jpeg'));
        $image->setValidator($image_validator);
        $fields->push($image);

        $presentation = new UploadField('Presentation','Presentation');
        $presentation->setFolderName('marketing');
        $fields->push($presentation);

        $fields->push(new HiddenField('ParentPageID','ParentPageID'));

        return $fields;
    }


}