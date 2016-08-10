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
class OpenStackDaysDoc extends DataObject {

    private static $db = array(
        'Label'     => 'Varchar(255)',
        'Group'     => 'Varchar(255)',
        'SortOrder' => 'Int',
    );

    private static $has_one = array(
        'OfficialGuidelines' => 'OpenStackDaysPage',
        'PlanningTools'      => 'OpenStackDaysPage',
        'Artwork'            => 'OpenStackDaysPage',
        'Media'              => 'OpenStackDaysPage',
        'Doc'                => 'File',
        'Thumbnail'          => 'BetterImage',
        'ParentPage'         => 'OpenStackDaysPage', //dummy
    );

    private static $summary_fields = array
    (
        'Label'     => 'Label',
        'Doc.Name'  => 'File',
    );

    public function getCMSFields() {
        $fields = new FieldList;

        $image = new UploadField('Thumbnail','Thumbnail');
        $image->setFolderName('openstackdays');
        $image->setAllowedFileCategories('image');

        $doc = new UploadField('Doc','Doc');
        $doc->setFolderName('openstackdays');
        $doc->getValidator()->setAllowedMaxFileSize(40*1024*1024);

        $fields->push(new TextField('Label'));
        $fields->push(new TextField('SortOrder'));
        $fields->push(new TextField('Group'));
        $fields->push($doc);
        $fields->push($image);

        return $fields;
    }

}