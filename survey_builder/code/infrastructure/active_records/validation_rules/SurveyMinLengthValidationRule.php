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

class SurveyMinLengthValidationRule extends SurveySingleValueValidationRule {

    const Name = 'minlength';


    static $db = array(
        'MinLength' => 'Int',
    );

    public function __construct($record = null, $isSingleton = false, $model = null) {
        parent::__construct($record, $isSingleton, $model);
        if($this->ID == 0) $this->Name = self::Name;
    }

    private static $defaults = array(
        'Name' => self::Name,
    );

    /**
     * @return string
     */
    public function name()
    {
        return $this->getField('Name');
    }


    /**
     * @return array()
     */
    public function getHtml5Attributes()
    {
        $attr = parent::getHtml5Attributes();

        $attr['data-rule-minlength'] = $this->MinLength;

        if(!empty($this->Message)){
            $attr['data-msg-minlength'] = $this->Message;
        }

        return $attr;
    }

    public function getCMSFields() {
        $fields = new FieldList();
        $fields->add($name = new TextField('Name','Name'));
        $fields->add(new NumericField('MinLength','Min Length'));
        $fields->add(new TextField('Message','Custom Message'));
        return $fields;
    }

}