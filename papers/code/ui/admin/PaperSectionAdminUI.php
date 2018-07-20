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

/**
 * Class PaperSectionAdminUI
 */
class PaperSectionAdminUI extends DataExtension
{

    /**
     * @var array
     */
    private static $summary_fields = [
        'Title'               => 'Title',
        'ClassName'           => 'Type',
    ];

    public function updateCMSFields(FieldList $f)
    {
        //clear all fields
        $oldFields = $f->toArray();
        foreach ($oldFields as $field) {
            $f->remove($field);
        }

        $f->add($rootTab = new TabSet("Root", $tabMain = new Tab('Main')));

        $f->addFieldToTab('Root.Main', new TextField('Title', 'Title'));

        $f->addFieldToTab('Root.Main', new TextField('Subtitle', 'Subtitle'));

        if ($this->owner->ID > 0) {
            // contents
            $config = GridFieldConfig_RecordEditor::create(50);
            $config->removeComponentsByType('GridFieldAddNewButton');
            $multi_class_selector = new GridFieldAddNewMultiClass();
            $multi_class_selector->setClasses
            (
                [
                    'PaperParagraph'     => 'Paragraph',
                    'PaperParagraphList' => 'List',
                ]
            );
            $config->addComponent($multi_class_selector);
            $config->addComponent($sort = new GridFieldSortableRows('Order'));
            $contents = new GridField('Contents', 'Contents', $this->owner->Contents(), $config);
            $f->addFieldToTab('Root.Main', $contents);

            // sub sections
            $config = GridFieldConfig_RecordEditor::create(50);
            $config->addComponent($sort = new GridFieldSortableRows('Order'));
            $subSections = new GridField('SubSections', 'SubSections', $this->owner->SubSections(), $config);
            $f->addFieldToTab('Root.Main', $subSections);
        }

        $f->addFieldToTab('Root.Main', new HiddenField('PaperID', 'PaperID'));
        $f->addFieldToTab('Root.Main', new HiddenField('ParentID', 'ParentID'));
    }
}