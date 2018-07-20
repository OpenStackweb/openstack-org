<?php
/**
 * Copyright 2018 OpenStack Foundation
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

class PaperParagraphListItemAdminUI extends DataExtension
{
    /**
     * @var array
     */
    private static $summary_fields = [
        'ID'       => 'ID',
        'Content'  => 'Content',
    ];

    public function updateCMSFields(FieldList $f)
    {
        //clear all fields
        $oldFields = $f->toArray();
        foreach ($oldFields as $field) {
            $f->remove($field);
        }

        $f->add($rootTab = new TabSet("Root", $tabMain = new Tab('Main')));
        $f->addFieldToTab('Root.Main', new HtmlEditorField('Content', 'Content'));
        $f->addFieldToTab('Root.Main', new HiddenField('OwnerID', 'OwnerID'));
        $f->addFieldToTab('Root.Main', new HiddenField('ParentID', 'ParentID'));

        $f->addFieldToTab('Root.Main', new DropdownField('SubItemsContainerType', 'Sub Items Container Type', $this->owner->dbObject('SubItemsContainerType')->enumValues()));

        if($this->owner->ID > 0){
            // sub sections
            $config = GridFieldConfig_RecordEditor::create(50);
            $config->addComponent($sort = new GridFieldSortableRows('Order'));
            $items = new GridField('SubItems', 'SubItems', $this->owner->SubItems(), $config);
            $f->addFieldToTab('Root.Main', $items);
        }
    }
}