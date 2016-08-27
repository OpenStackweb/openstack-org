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
class MarketingFile extends File {

    private static $db = array(
        'SortOrder' => 'Int',
        'Group'     => 'Varchar(255)',
    );

    private static $has_one = array(
        'CollateralFiles'  => 'MarketingCollateral',
    );

    public function getCMSFields() {
        $fields = parent::getCMSFields();
        $fields->removeByName('Name');
        $fields->removeByName('OwnerID');
        $fields->removeByName('ParentID');
        $fields->addFieldToTab('Root.Main', new TextField('SortOrder'));
        $fields->addFieldToTab('Root.Main', new TextField('Group'));

        return $fields;
    }

}