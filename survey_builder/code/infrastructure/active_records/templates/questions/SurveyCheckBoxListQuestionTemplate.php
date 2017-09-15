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

class SurveyCheckBoxListQuestionTemplate
    extends SurveyMultiValueQuestionTemplate
    implements ISurveyClickableQuestion {

    static $db = array
    (
        'DefaultGroupLabel' => 'HTMLText',
    );

    static $has_many = [
        'Groups' => 'SurveyQuestionValueTemplateGroup'
    ];

    public function Type(){
        return 'CheckBoxList';
    }

    public function getCMSFields() {

        $fields = parent::getCMSFields();
        $fields->removeByName('EmptyString');

        if($this->ID > 0 ){
            $fields->removeByName('DefaultValueID');
            $fields->add(new HtmlEditorField("DefaultGroupLabel", "Default Group Label <small>( this group will include all values without group assigned)</small>") );
            $config = GridFieldConfig_RecordEditor::create(PHP_INT_MAX);
            $config->addComponent(new GridFieldSortableRows('Order'));
            $gridField = new GridField('Groups', 'Values Groups', $this->Groups(), $config);
            $add_button = $config->getComponentByType('GridFieldAddNewButton');
            $add_button->setButtonName('Add New Values Group');
            $config->getComponentByType('GridFieldDataColumns')->setDisplayFields(
                [
                    'ID'    => 'ID',
                    'Label' => 'Label',
                ]
            );

            $fields->add($gridField);
        }

        return $fields;
    }

    /**
     * @return mixed|string
     */
    public function getDefaultGroupLabel(){
        $label = $this->getField("DefaultGroupLabel");
        return empty($label) ? "Other" : $label;
    }
}