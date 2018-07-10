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
 * Class PaperAdminUI
 */
final class PaperAdminUI extends DataExtension
{
    /**
     * @var array
     */
    private static $summary_fields = [
        'Title'                   => 'Title',
        'Creator.Email'           => 'Creator',
    ];

    public function updateCMSFields(FieldList $f)
    {
        //clear all fields
        $oldFields = $f->toArray();
        foreach ($oldFields as $field) {
            $f->remove($field);
        }

        $f->add($rootTab = new TabSet("Root", $tabMain = new Tab('Main')));

        $logo_field = new UploadField('BackgroundImage', 'Header Background Image');
        $logo_field->setAllowedMaxFileNumber(1);
        $logo_field->setAllowedFileCategories('image');
        $logo_field->setFolderName('papers/headers');
        $logo_field->getValidator()->setAllowedMaxFileSize(1024*1024*1);
        $f->addFieldToTab('Root.Main', $logo_field);

        $f->addFieldToTab('Root.Main', new TextField('Title', 'Title'));

        $f->addFieldToTab('Root.Main', new TextField('Subtitle', 'Subtitle'));

        $f->addFieldToTab('Root.Main', new HtmlEditorField('Abstract', 'Abstract'));

        $f->addFieldToTab('Root.Main', new HtmlEditorField('Footer', 'Footer'));

        if ($this->owner->ID > 0) {
            // sections
            $config = GridFieldConfig_RecordEditor::create(50);
            $config->removeComponentsByType('GridFieldAddNewButton');
            $multi_class_selector = new GridFieldAddNewMultiClass();
            $multi_class_selector->setClasses
            (
                [
                    'PaperSection'          => 'Regular Section',
                    'CaseOfStudySection'    => 'Case Of Study Section',
                    'IndexSection'          => 'Index Section',
                ]
            );
            $config->addComponent($multi_class_selector);
            $config->addComponent($sort = new GridFieldSortableRows('Order'));
            $gridField = new GridField('Sections', 'Sections', $this->owner->Sections(), $config);
            $f->addFieldToTab('Root.Sections', $gridField);

            // contributors

            //$config = GridFieldConfig_RecordEditor::create(50);
            //$contributors = new GridField('Contributors', 'Contributors', $this->owner->Contributors(), $config);
            //$f->addFieldToTab('Root.Contributors', $contributors);
        }
    }
}