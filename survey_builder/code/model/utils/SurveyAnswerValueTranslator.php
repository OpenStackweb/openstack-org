<?php
/**
 * Copyright 2017 OpenStack Foundation
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
 * Class SurveyAnswerValueTranslator
 */
final class SurveyAnswerValueTranslator
{

    /**
     * @param $old_value
     * @param ISurveyQuestionTemplate $old_question
     * @param ISurveyQuestionTemplate $new_question
     * @return string
     */
    public static function translate($old_value, ISurveyQuestionTemplate $old_question, ISurveyQuestionTemplate $new_question)
    {
        if(is_null($old_value)) return null;

        $new_value = $old_value;

        if($old_question instanceof IDoubleEntryTableQuestionTemplate && $new_question instanceof IDoubleEntryTableQuestionTemplate){
            $new_tuples = [];

            foreach (explode(',', $old_value) as $tuple) {
                list($row_id, $col_id) = explode(':', $tuple);
                $col     = SurveyQuestionColumnValueTemplate::get()->byID($col_id);
                if(is_null($col)) continue;
                $row     = SurveyQuestionRowValueTemplate::get()->byID($row_id);
                if(is_null($row)) continue;

                $col_val = $col->Value;
                $row_val = $row->Value;

                $new_col = SurveyQuestionColumnValueTemplate::get()->filter(
                    [
                        'Value' => $col_val,
                        'OwnerID' => $new_question->ID
                    ]
                )->first();
                $new_row = SurveyQuestionRowValueTemplate::get()->filter(
                    [
                        'Value'   => $row_val,
                        'OwnerID' => $new_question->ID
                    ]
                )->first();

                if (is_null($new_col) || is_null($new_row)) continue;

                $new_tuples[] = "{$new_row->ID}:{$new_col->ID}";
            }

            return implode(',', $new_tuples);
        }

        if($old_question instanceof IMultiValueQuestionTemplate && $new_question instanceof IMultiValueQuestionTemplate)
        {
            if($old_question instanceof IDropDownQuestionTemplate && $old_question->isCountrySelector() &&
                $new_question instanceof IDropDownQuestionTemplate && $new_question->isCountrySelector())
                return $old_value;
            // need translate value
            $old_value = explode(',', $old_value);
            $new_value =  array();
            foreach($old_question->getValues() as $val_aux)
            {
                $id = $val_aux->getIdentifier();
                if(in_array($id, $old_value))
                {
                    // this value its present on old answer
                    $new_val = $new_question->getValueByValue
                    (
                        $val_aux->value()
                    );
                    if(is_null($new_val)) continue;
                    $new_id = $new_val->getIdentifier();
                    array_push($new_value, $new_id  );
                }
            }
            return implode(',', $new_value);
        }
        return $new_value;
    }
}