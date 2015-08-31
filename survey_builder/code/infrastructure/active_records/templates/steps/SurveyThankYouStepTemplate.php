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
 * Class SurveyThankYouStepTemplate
 */
class SurveyThankYouStepTemplate
    extends SurveyStepTemplate
    implements ISurveyThankYouStepTemplate
{

    static $db = array
    (

    );

    static $has_one = array(

    );

    static $many_many = array(
    );

    static $has_many = array(

    );

    private static $defaults = array(
    );


    public function __construct($record = null, $isSingleton = false, $model = null){
        parent::__construct($record, $isSingleton, $model);
        $this->SkipStep = false;
    }

    public function getCMSFields()
    {
        $fields = new FieldList();
        $fields->add(new HtmlEditorField('Content','Content'));
        $fields->add(new TextField('FriendlyName','Friendly Name'));
        $fields->add(new HiddenField('SurveyStepTemplateID','SurveyStepTemplateID'));
        return $fields;
    }

    function getValidator()
    {
        $validator_fields  = new RequiredFields(array('FriendlyName','Content'));

        return $validator_fields;
    }

    protected function onAfterWrite() {
        parent::onAfterWrite();
        $order = count($this->survey()->getSteps());
        $id    = $this->ID;
        DB::query(" UPDATE SurveyStepTemplate SET `Order` = {$order} WHERE ID = {$id} ");
    }

    protected function onBeforeWrite() {
        parent::onBeforeWrite();
    }

}