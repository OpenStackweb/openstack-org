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

class SurveyCheckBoxQuestionTemplate
    extends SurveySingleValueTemplateQuestion
    implements ISurveyClickableQuestion {

    static $db = array(
    );

    static $has_one = array(
    );

    static $indexes = array(
    );

    static $belongs_to = array(

    );

    static $many_many = array(
    );

    static $has_many = array(

    );

    private static $defaults = array(
    );

    public function Type(){
        return 'CheckBox';
    }

    public function JSONValues(){
        $values = [];

        $values[] = [
            'id'    => 1,
            'label' => 'True',
        ];

        $values[] = [
            'id'    => 0,
            'label' => 'False',
        ];

        return json_encode($values);
    }
}