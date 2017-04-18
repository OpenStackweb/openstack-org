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
final class OpenStackApiVersionAdminUI extends DataExtension
{

    private static $searchable_fields = array('Type');

    /**
     * @param FieldList $fields
     * @return FieldList|void
     */
    public function updateCMSFields(FieldList $fields)
    {
        $oldFields = $fields->toArray();
        foreach ($oldFields as $field) {
            $fields->remove($field);
        }
        $fields->push(new LiteralField("Title", "<h2>OpenStack Api Version</h2>"));
        $fields->push(new TextField("Version", "Version"));
        $fields->push(new CheckboxField('CreatedFromTask', 'Created From Task?'));
        $fields->push(new DropdownField(
            'Status',
            'Status',
            $this->owner->dbObject('Status')->enumValues()
        ));

        return $fields;
    }

    public function onBeforeWrite()
    {
        //create group here?
        parent::onBeforeWrite();
    }

    function getCMSValidator()
    {
        return $this->getValidator();
    }

    function getValidator()
    {
        $validator = new RequiredFields(array('Version'));

        return $validator;
    }
}