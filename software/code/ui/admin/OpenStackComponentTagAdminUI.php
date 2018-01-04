<?php

/**
 * Copyright 2017 Openstack Foundation
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
final class OpenStackComponentTagAdminUI extends DataExtension
{

    private static $summary_fields = [
        'Name'          => 'Name',
        'Type'          => 'Type',
        'Label'         => 'Label',
        'Enabled'       => 'Enabled'
    ];

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

        $fields->push(new LiteralField("Title", "<h2>OpenStack Component Tag</h2>"));
        $fields->push(new TextField("Name", "Name"));
        $fields->push(new DropdownField("Type", "Type", $this->owner->dbObject('Type')->enumValues()));
        $fields->push(new TextField("Label", "Label"));
        $fields->push(new TextField("Description", "Description"));
        $fields->push(new TextField("Link", "Link"));
        $fields->push(new TextField("LabelTranslationKey", "Label Translation Key"));
        $fields->push(new TextField("DescriptionTranslationKey", "Description Translation Key"));
        $fields->push(new CheckboxField("Enabled", "Enabled?"));

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
        $validator = new RequiredFields(array('Name'));

        return $validator;
    }

}