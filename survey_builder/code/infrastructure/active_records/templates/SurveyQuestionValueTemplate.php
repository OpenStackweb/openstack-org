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

class SurveyQuestionValueTemplate extends DataObject {

    static $db = array(
        'Label'          => 'Varchar(255)',
        'Value'          => 'Varchar(255)',
        'IsOther'        => 'Boolean',
        'OtherSubtitle'  => 'Varchar(255)',
    );

    static $has_one = array(
        'Owner' => 'SurveyMultiValueQuestionTemplate',
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
        'IsOther' => false,
    );


    private static $summary_fields = array(
        'Label',
        'Value',
    );
}