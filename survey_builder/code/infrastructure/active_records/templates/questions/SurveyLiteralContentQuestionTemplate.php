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

/**
 * Class SurveyLiteralContentQuestionTemplate
 */
final class SurveyLiteralContentQuestionTemplate extends SurveyQuestionTemplate {

    static $db = array(
        'Content' => 'HTMLText',
    );

    private static $defaults = array(
        'ReadOnly'  => true,
        'Mandatory' => false,
    );

    public function Type(){
        return 'Literal';
    }

    protected function validate() {
        $valid = ValidationResult::create();
        if(!$valid->valid()) return $valid;

        if(empty($this->Name)){
            return $valid->error('Name is empty!');
        }

        $survey_template_id = intval($this->Step()->SurveyTemplateID);

        $res = DB::query("SELECT COUNT(Q.ID) FROM SurveyQuestionTemplate Q
                          INNER JOIN `SurveyStepTemplate` S ON S.ID = Q.StepID
                          INNER JOIN `SurveyTemplate` T ON T.ID = S.SurveyTemplateID
                          WHERE Q.Name = '{$this->Name}' AND Q.ID <> {$this->ID} AND T.ID = {$survey_template_id};")->value();

        if (intval($res) > 0) {
            return $valid->error('There is already another Question on the survey with that name!');
        }

        return $valid;
    }

    public function getCMSFields() {
        $fields = new FieldList();
        $fields->add(new TextField('Name','Name (Without Spaces)'));
        $fields->add(new HtmlEditorField('Content', 'Content'));
        return $fields;
    }
}