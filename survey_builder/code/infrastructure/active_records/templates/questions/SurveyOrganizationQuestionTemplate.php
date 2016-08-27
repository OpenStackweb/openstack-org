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

class SurveyOrganizationQuestionTemplate
    extends SurveySingleValueTemplateQuestion {

    public function Type(){
        return SurveyOrganizationQuestionTemplate::FieldName;
    }

    const FieldName = 'Organization';

    public function getCMSFields() {

        $_REQUEST["SurveyQuestionTemplateID"] = $this->ID;

        $fields = new FieldList();
        $fields->add(new TextField('Label','Label'));
        $fields->add(new CheckboxField('Mandatory','Is Mandatory?'));
        $fields->add(new HiddenField('Name','Name',SurveyOrganizationQuestionTemplate::FieldName));
        $fields->add(new CheckboxField('ShowOnSangriaStatistics','Show on Sangria statistics?'));
        $fields->add(new CheckboxField('ShowOnPublicStatistics','Show on Public statistics?'));
        $fields->add(new CheckboxField('Hidden','Hide on front-end?'));
        return $fields;
    }

    protected function onBeforeWrite() {
        parent::onBeforeWrite();
        $this->Name = SurveyOrganizationQuestionTemplate::FieldName;
    }
}