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

class SurveyCustomValidationRule
    extends SurveySingleValueValidationRule
    implements ICustomValidationRule{

    static $db = array(
        'CustomJSMethod'=> 'Text',
    );

    static $has_one = array(
    );


    static $belongs_to = array(
    );

    static $many_many = array(
    );

    static $has_many = array(
    );

    private static $defaults = array(
        'CustomJSMethod' => 'function(value, element, params) { return true; }'
    );

    /**
     * @return void
     */
    public function registerRule()
    {

        if(!empty($this->CustomJSMethod)){
            $script = "jQuery.validator.addMethod('%s', %s %s);";
            $script = printf($script, $this->name(), $this->CustomJSMethod, $this->hasCustomMessage()? ",'".$this->CustomJSMethod."'" : '' );
            Requirements::customScript(uniqid($this->name()) , $script);
        }
    }
}