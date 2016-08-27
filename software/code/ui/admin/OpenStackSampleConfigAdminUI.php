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
class OpenStackSampleConfigAdminUI extends DataExtension
{
    private static $summary_fields = array
    (
        'Title'          => 'Title',
        'Type.Type'      => 'Type',
        'Release.Name'   => 'Release',
        'Curator.Email'  => 'Curator',
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

        $fields->push(new TextField("Title", "Title"));
        $fields->push(new HtmlEditorField("Summary", "Summary"));
        $fields->push(new HtmlEditorField("Description", "Description"));

        $fields->push(new MemberAutoCompleteField("Curator", "Curator"));
        $fields->push($ddl = new DropdownField(
            'ReleaseID',
            'Release',
            OpenStackRelease::get()->map("ID", "Name")
        ));

        $ddl->setEmptyString('-- Select a Release --');

        if($this->owner->ID > 0) {

            $components_config = new GridFieldConfig_RelationEditor(100);
            $components        = new GridField
            (
                "OpenStackComponents",
                "Supported Release Components",
                $this->owner->OpenStackComponents(),
                $components_config
            );

            $components_config->getComponentByType('GridFieldAddExistingAutocompleter')->setSearchList($this->getAllowedComponents());
            $components_config->removeComponentsByType('GridFieldAddNewButton');
            //$components_config->addComponent(new GridFieldSortableRows('OpenStackSampleConfig_OpenStackComponents.Order'));

            $fields->push($components);

            $fields->push($ddl = new DropdownField(
                'TypeID',
                'Type',
                OpenStackSampleConfigurationType::get()->filter('ReleaseID', $this->owner->ReleaseID)->map("ID", "Type")
            ));

            $ddl->setEmptyString('-- Select a Configuration Type --');


            $related_notes_config =  new GridFieldConfig_RecordEditor(100);

            $related_notes        = new GridField
            (
                "RelatedNotes",
                "Related Notes",
                $this->owner->RelatedNotes(),
                $related_notes_config
            );

            $related_notes_config->addComponent(new GridFieldSortableRows('Order'));

            $fields->push($related_notes);

        }
        return $fields;
    }

    public function getAllowedComponents()
    {
        $release_id = intval($this->owner->ReleaseID);
        if($release_id > 0)
            return OpenStackRelease::get()->byID($release_id)->OpenStackComponents();
        return new ArrayList();
    }

    public function validate(ValidationResult $valid)
    {
        if(!$valid->valid()) return $valid;

        if(empty($this->owner->Title)){
            return $valid->error('Title is empty!');
        }

        if(intval($this->owner->ReleaseID) === 0 ){
            return  $valid->error('You must select a release');
        }

        if(intval($this->owner->CuratorID) === 0 ){
            return  $valid->error('You must select a Curator');
        }

        return $valid;
    }
}