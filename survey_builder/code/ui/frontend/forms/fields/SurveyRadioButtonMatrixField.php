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
class SurveyRadioButtonMatrixField extends FormField
{

    /**
     * @var IDoubleEntryTableQuestionTemplate
     */
    private $question;

    /**
     * @return IDoubleEntryTableQuestionTemplate
     */
    public function Question()
    {
        return $this->question;
    }

    /**
     * @var ISurveyAnswer
     */
    private $answer;



    public function setAnswer(ISurveyAnswer $answer)
    {
        $this->answer = $answer;
    }

    /**
     * @param string $name
     * @param null $title
     * @param IDoubleEntryTableQuestionTemplate $question
     * @param ISurveyAnswer|null $answer
     */
    public function __construct
    (
        $name,
        $title = null,
        IDoubleEntryTableQuestionTemplate $question,
        ISurveyAnswer $answer = null
    )
    {
        parent::__construct($name, $title);
        $this->question = $question;
        $this->answer   = $answer;
    }

    public function Field($properties = array())
    {

        Requirements::css('survey_builder/css/survey.radio.button.matrix.field.css');
        Requirements::javascript('survey_builder/js/survey.radio.button.matrix.field.js');
        Requirements::customScript(
            "
                jQuery(document).ready(function($){
                    $('#{$this->name}').survey_radio_button_matrix_field();
                });
            ");

        $cols = new ArrayList($this->question->getColumns());
        $rows = new ArrayList($this->question->getRows());
        $already_added_additional_rows = new ArrayList();
        $additional_rows = new ArrayList($this->question->getAlternativeRows());

        if(!is_null($this->answer) &&  $answer_value = $this->answer->value() && !empty($answer_value))
        {
            $tuples           = explode(',', $answer_value);
            $exclude_row_list = array();

            foreach($tuples as $t)
            {
                list($row_id, $col_id ) = explode(':', $t);
                if($this->question->isAlternativeRow($row_id))
                {
                    //already added
                    $already_added_additional_rows->add($this->question->getAlternativeRow($row_id));
                    array_push($exclude_row_list, $row_id);
                }
            }
            if(count($exclude_row_list))
            {
                $additional_rows = new ArrayList($this->question->getAlternativeRows(implode(',', $exclude_row_list)));
            }
        }

        foreach($rows as $r)
        {
            $r->Columns = $cols;
        }

        foreach($additional_rows as $r)
        {
            $r->Columns = $cols;
        }

        foreach($already_added_additional_rows as $r)
        {
            $r->Columns = $cols;
        }

        $properties['Columns'] = $cols;
        $properties['Rows'] = $rows;
        $properties['AdditionalRows'] = $additional_rows;
        $properties['AlreadyAddedAdditionalRows'] = $already_added_additional_rows;
        $properties['RowsLabel'] = $this->question->RowsLabel;
        $properties['AdditionalRowsLabel'] = $this->question->AdditionalRowsLabel;
        $properties['AdditionalRowsDescription'] = $this->question->AdditionalRowsDescription;
        $properties['EmptyString'] = $this->question->EmptyString;

        return $this
            ->customise($properties)
            ->renderWith(array("SurveyRadioButtonMatrixField"));
    }

    /**
     * @param $row_id
     * @param $col_id
     * @return bool
     */
    public function isChecked($row_id, $col_id)
    {
        if(is_null($this->answer)) return false;
        $tuple = sprintf('%s:%s',$row_id, $col_id);
        return !(strstr($this->answer->value(), $tuple) === false);
    }

    protected $validation_attributes = array();

    public function setValidationAttribute($name, $value) {
        $this->validation_attributes[$name] = $value;
        return $this;
    }

    /**
     * @return array
     */
    public function getValidationAttributes() {
        return  $this->validation_attributes;
    }

    /**
     * @param Array Custom attributes to process. Falls back to {@link getAttributes()}.
     * If at least one argument is passed as a string, all arguments act as excludes by name.
     * @return string HTML attributes, ready for insertion into an HTML tag
     */
    public function getValidationAttributesHTML($attrs = null) {
        $exclude = (is_string($attrs)) ? func_get_args() : null;

        if(!$attrs || is_string($attrs)) $attrs = $this->getValidationAttributes();

        // Remove empty
        $attrs = array_filter((array)$attrs, function($v) {
            return ($v || $v === 0 || $v === '0');
        });

        // Remove excluded
        if($exclude) $attrs = array_diff_key($attrs, array_flip($exclude));

        // Create markkup
        $parts = array();
        foreach($attrs as $name => $value) {
            $parts[] = ($value === true) ? "{$name}=\"{$name}\"" : "{$name}=\"" . Convert::raw2att($value) . "\"";
        }

        return implode(' ', $parts);
    }

}