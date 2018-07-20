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

class CaseOfStudySectionAdminUI extends PaperSectionAdminUI
{
    public function updateCMSFields(FieldList $f)
    {
        parent::updateCMSFields($f);
        $f->removeFieldFromTab('Root.Main', "Contents");
        if($this->owner->ID > 0)
        {
            $f->removeFieldFromTab('Root.Main', 'SubSections');
            // sub sections
            $config = GridFieldConfig_RecordEditor::create(50);
            $config->removeComponentsByType('GridFieldAddNewButton');
            $multi_class_selector = new GridFieldAddNewMultiClass();
            $multi_class_selector->setClasses
            (
                [
                    'CaseOfStudy'     => 'Case of Study',
                ]
            );
            $config->addComponent($multi_class_selector);
            $config->addComponent($sort = new GridFieldSortableRows('Order'));
            $subSections = new GridField('SubSections', 'Case of Studies', $this->owner->SubSections(), $config);
            $f->addFieldToTab('Root.Main', $subSections);
        }
    }
}