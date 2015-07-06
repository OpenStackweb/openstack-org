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

class SurveyCheckboxSetField extends CustomCheckboxSetField {

    /**
     * @var bool
     */
    private $required;

    /**
     * @var bool
     */
    private $visible;

    /**
     * @var IMultiValueQuestionTemplate
     */
    private $question;

    public function setRequired(){
        $this->required = true;
    }

    public function isRequired(){
        return  $this->required;
    }


    public function setVisible($visible){
        $this->visible = $visible;
    }

    public function isVisible(){
        return  $this->visible;
    }


    public function __construct($name, $title=null, $source=array(), $value='', $form=null, $emptyString=null, IMultiValueQuestionTemplate $question) {
        parent::__construct($name, $title, $source, $value, $form, $emptyString);
        $this->visible  = true;
        $this->question = $question;
    }

    public function addExtraClass($class) {
        if($class === 'hidden') $this->setVisible(false);
        return parent::addExtraClass($class);
    }
}