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
class MarketingDoc extends DataObject {

    private static $db = array(
        'Label'     => 'Varchar(255)',
        'GroupName' => 'Varchar(255)',
        'SortOrder' => 'Int',
    );

    private static $has_one = array(
        'Stickers'         => 'MarketingPage',
        'TShirts'          => 'MarketingPage',
        'Banners'          => 'MarketingPage',
        'Thumbnail'        => 'BetterImage',
        'Doc'              => 'File',
        'ParentPage'       => 'MarketingPage',
    );

    private static $summary_fields = array
    (
        'Label'     => 'Label',
        'Doc.Name'  => 'File',
    );

    public function getCMSFields() {
        $fields = new FieldList;

        $image = new UploadField('Thumbnail','Thumbnail');
        $image->setFolderName('marketing');
        $image->setAllowedFileCategories('image');

        $doc = new UploadField('Doc','Doc');
        $doc->setFolderName('marketing');
        $doc->getValidator()->setAllowedMaxFileSize(40*1024*1024);

        $fields->push(new TextField('Label'));
        $fields->push(new TextField('GroupName','Group (leave this empty if singular)'));
        $fields->push(new TextField('SortOrder'));
        $fields->push($doc);
        $fields->push($image);

        return $fields;
    }

}