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
final class OpenStackComponentCategoryAdminUI extends DataExtension
{

    private static $summary_fields = [
        'Name'          => 'Name',
        'Description'   => 'Description'
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
        $fields->push(new LiteralField("Title", "<h2>OpenStack Component Category</h2>"));
        $fields->push(new TextField("Name", "Name"));
        $fields->push(new TextField("Description", "Description"));

        if($this->owner->ID) {
            $config = new GridFieldConfig_RelationEditor();
            $config->addComponent(new GridFieldSortableRows('Order'));
            $sub_categories = new GridField("SubCategories", "Sub-Categories", $this->owner->SubCategories(), $config);
            $fields->push($sub_categories);

            $config = new GridFieldConfig_RelationEditor();
            $config->addComponent(new GridFieldSortableRows('Order'));
            $components = new GridField("OpenStackComponents", "Components", $this->owner->OpenStackComponents(), $config);
            $fields->push($components);
        }


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