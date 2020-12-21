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
final class OpenStackComponentAdminUI extends DataExtension
{

    private static $searchable_fields = array('Name', 'CodeName');

    private static $summary_fields = array
    (
        'CodeName',
        'Name',
        'IsCoreService',
    );

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
        $fields->push(new LiteralField("Title", "<h2>OpenStack Component</h2>"));
        $fields->push(new TextField("Name", "Name"));
        $fields->push(new TextField("CodeName", "Code Name"));
        $fields->push($mascot_ddl = new DropdownField('MascotID', 'Mascot',  Mascot::get()->map('ID', 'Name')));
        $mascot_ddl->setEmptyString("-- Select Mascot --");
        $fields->push(new TextField("Description", "Description"));
        $fields->push(new TextField("YouTubeID", "Video (YouTubeID)"));
        $fields->push(new TextField("VideoTitle", "Video Title"));
        $fields->push(new ReadonlyField("Slug", "Slug"));
        $fields->push(new TextField("DocumentationSlug", "Documentation Slug"));
        $fields->push(new TextareaField("VideoDescription", "Video Description"));
        $fields->push(new CheckboxField('SupportsVersioning', 'Supports Versioning?'));
        $fields->push(new CheckboxField('SupportsExtensions', 'Supports Extensions?'));
        $fields->push(new CheckboxField('IsCoreService', 'Is Core Service?'));
        $fields->push(new CheckboxField('ShowOnMarketplace', 'Show On Marketplace?'));
        $categories = OpenStackComponentCategory::get()->map('ID', 'Name');
        $fields->push($cat_ddl = new DropdownField('CategoryID', 'Category',  $categories));
        $cat_ddl->setEmptyString("-- Select Category --");

        if ($this->owner->getSupportsVersioning()) {
            $versions_config = new GridFieldConfig_RecordEditor();
            $versions = new GridField("Versions", "Versions", $this->owner->Versions(), $versions_config);
            $fields->push($versions);
        }

        $config = new GridFieldConfig_RecordEditor();
        $config->addComponent(new GridFieldSortableRows('Order'));
        $related_content = new GridField("RelatedContent", "RelatedContent", $this->owner->RelatedContent(), $config);
        $fields->push($related_content);

        $config = new GridFieldConfig_RecordEditor();
        $config->addComponent(new GridFieldSortableRows('SortOrder'));
        $component_tags = new GridField("Tags", "Component Tags", $this->owner->Tags(), $config);
        $fields->push($component_tags);

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
        $validator = new RequiredFields(array('Name', 'CodeName'));

        return $validator;
    }

}