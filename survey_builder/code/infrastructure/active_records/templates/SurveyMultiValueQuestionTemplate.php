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

class SurveyMultiValueQuestionTemplate extends SurveyQuestionTemplate {

    static $db = array(

    );

    static $has_one = array(
        'DefaultValue' => 'SurveyQuestionValueTemplate',
    );

    static $indexes = array(

    );

    static $belongs_to = array(

    );

    static $many_many = array(
    );

    static $has_many = array(
        'Values' => 'SurveyQuestionValueTemplate'
    );

    private static $defaults = array(

    );

    public function getCMSFields() {

        $fields = new FieldList();

        $fields->add(new TextField('Name','Name'));
        $fields->add(new TextField('Label','Label'));
        $fields->add(new CheckboxField('Mandatory','Is Mandatory?'));
        $fields->add(new CheckboxField('ReadOnly','Is Read Only?'));

        if($this->ID > 0 ){
            //validation rules
            $config = GridFieldConfig_RecordEditor::create();
            $gridField = new GridField('Values', 'Values', $this->Values(), $config);
            $fields->add($gridField);

            if($this->Values()->count() > 0){

                $fields->add($ddl_default = new DropdownField(
                    'DefaultValueID',
                    'Please choose an default value',
                    $this->Values()->map("ID", "Label")
                ));
                $ddl_default->setEmptyString('-- select --');
            }
        }


        return $fields;
    }


}