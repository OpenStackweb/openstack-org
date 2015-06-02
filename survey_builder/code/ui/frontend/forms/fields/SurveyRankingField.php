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

class SurveyRankingField extends OptionsetField {

    /**
     * @var bool
     */
    private $required;

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

    public function Question(){
        return $this->question;
    }

    public function __construct($name, $title=null, $source=array(), $value='', $form=null, $emptyString=null, IMultiValueQuestionTemplate $question) {
        parent::__construct($name, $title, $source, $value, $form, $emptyString);
        $this->question = $question;
    }


    public function Field($properties = array()) {

        Requirements::css('survey_builder/css/survey.ranking.field.css');
        Requirements::javascript('survey_builder/js/survey.raking.field.js');

        $source = $this->getSource();
        $odd = 0;
        $options = array();

        if($source) {
            foreach($source as $id => $label) {
                $odd = ($odd + 1) % 2;
                $extraClass  = $odd ? 'odd' : 'even';
                $extraClass .= ' val' . preg_replace('/[^a-zA-Z0-9\-\_]/', '_', $id);
                $options[]   = new ArrayData(array(
                    'ID'      => $id,
                    'Class'   => $extraClass,
                    'Name'    => $this->name,
                    'Title'   => $label,
                ));
            }
        }

        $properties = array_merge($properties, array(
            'Options' => new ArrayList($options)
        ));

        return $this->customise($properties)->renderWith(
            $this->getTemplates()
        );
    }

    public function AnswerIndex($value_id){

        $values = (empty($this->value))? array():explode(',', $this->value);
        $values = (count($values) > 0) ? array_combine($values, $values): array();

        $index = false;
        if(isset($values[$value_id])){
            $index = array_search($value_id, array_keys($values));
        }
        return $index !== false ? $index+1 : false;
    }

    public function AnswerCount(){
        $values = (empty($this->value))? array():explode(',', $this->value);
        return count($values);
    }

}