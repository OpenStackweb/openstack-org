<?php
/**
 * Copyright 2015 OpenStack Foundation
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

class SummitNetworkingPhoto extends DataObject {

    private static $has_one = array(
        'Image' => 'BetterImage',
        'Owner' => 'SummitOverviewPage'
    );

    private static $db = array(
        'Order'    => 'Int',
    );

    public function getCMSFields()
    {
        $fields = new FieldList();
        $fields->add($upload_0 = new UploadField('Image','Photo'));
        $upload_0->setFolderName('summits/overview/networking');
        $upload_0->setAllowedMaxFileNumber(1);
        $upload_0->setAllowedFileCategories('image');
        return $fields;
    }

}