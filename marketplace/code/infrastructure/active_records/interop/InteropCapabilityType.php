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

class InteropCapabilityType  extends DataObject {

    static $db = array(
        'Name'  => 'Varchar',
    );

    private static $has_many = array(
        'Capabilities' => 'InteropCapability',
    );

    function getCMSFields()
    {
        $fields =  new FieldList();
        $fields->add(new TextField('Name','Name'));
        return $fields;
    }

    public function canView($member = null) {
        return Permission::check('CMS_ACCESS_InteropAdmin', 'any', $member);
    }

    public function canEdit($member = null) {
        return Permission::check('CMS_ACCESS_InteropAdmin', 'any', $member);
    }

    public function canDelete($member = null) {
        return Permission::check('CMS_ACCESS_InteropAdmin', 'any', $member);
    }

    public function canCreate($member = null) {
        return Permission::check('CMS_ACCESS_InteropAdmin', 'any', $member);
    }
}