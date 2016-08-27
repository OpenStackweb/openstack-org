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
class MarketingCollateral extends DataObject {

    private static $db = array(
        'Name'        => 'Varchar(255)',
        'Description' => 'HTMLText',
        'ShowGlobe'   => 'Boolean',
        'SortOrder'   => 'Int',
    );

    private static $has_one = array(
        'ParentPage'     => 'MarketingPage',
        'Image'          => 'BetterImage'
    );

    private static $has_many = array(
        'CollateralFiles'    => 'MarketingFile.CollateralFiles',
        'CollateralLinks'    => 'MarketingLink',
    );

    function getCMSFields(){

        $fields = new FieldList;

        $fields->push(new TextField('Name','Name'));
        $fields->push(new CheckboxField('ShowGlobe','Show Globe'));
        $fields->push($description = new HtmlEditorField('Description'));
        $description->setRows(5);
        $fields->push(new TextField('SortOrder','Sort Order'));

        $image = new CustomUploadField('Image','Image');
        $image->setFolderName('marketing');
        $image->setAllowedFileCategories('image');

        $image_validator = new Upload_Validator();
        $image_validator->setAllowedExtensions(array('jpg','png','jpeg'));
        $image->setValidator($image_validator);
        $fields->push($image);

        $files = new UploadField('CollateralFiles','Files');
        $files->setFolderName('marketing');
        $fields->push($files);

        $config = new GridFieldConfig_RecordEditor(3);
        $config->addComponent(new GridFieldSortableRows('SortOrder'));
        $fields->push(new GridField('CollateralLinks', 'CollateralLinks', $this->CollateralLinks(), $config));

        $fields->push(new HiddenField('ParentPageID','ParentPageID'));

        return $fields;
    }

}