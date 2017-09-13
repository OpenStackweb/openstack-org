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
 * Class DeploymentToolsMergeAnsibleAnswersMigration
 */
final class DeploymentToolsMergeAnsibleAnswersMigration extends AbstractDBMigrationTask
{
    protected $title = "DeploymentToolsMergeAnsibleAnswersMigration";

    protected $description = "DeploymentToolsMergeAnsibleAnswersMigration";

    const ValuePattern = 'Ansible';

    const MinThreshold = 2;

    function doUp()
    {
        global $database;

        $questions = SurveyQuestionTemplate::get()->filter('Name', 'DeploymentTools');
        foreach ($questions as $question) {
            if (!$question instanceof SurveyMultiValueQuestionTemplate) continue;
            $options_to_merge = [];
            foreach ($question->getValues() as $option) {
                if (strpos(strtolower($option->Value), strtolower(self::ValuePattern)) === 0)
                    $options_to_merge[] = $option;
            }
            if (count($options_to_merge) < self::MinThreshold) continue;
            // create new option
            $new_option = new SurveyQuestionValueTemplate();
            $new_option->Value   = self::ValuePattern;
            $new_option->Label   = self::ValuePattern;
            $new_option->Order   = $options_to_merge[0]->Order;
            $new_option->OwnerID = $question->ID;
            $new_option->write();

            $where_condition = '';
            $values_2_replace = [];
            foreach ($options_to_merge as $option){
                $value = $option->ID;
                if(!empty($where_condition)) $where_condition .= " OR ";
                $where_condition .= " Value LIKE '%{$value}%' ";
                $values_2_replace[] = intval($value);
            }

            $where_condition = "( ".$where_condition." )";

            // answers
            $answers = SurveyAnswer::get()->filter([
                'QuestionID' => $question->ID,

            ])->where($where_condition);

            foreach($answers as $answer){
                $replaced   = false;
                $new_values = [];
                foreach (explode(",", $answer->Value) as $answer_value){
                    if(in_array(intval($answer_value), $values_2_replace)){
                        if($replaced) continue;
                        $replaced = true;
                        $new_values[] = intval($new_option->ID);
                        continue;
                    }
                    $new_values[] = intval($answer_value);
                }
                $answer->Value = implode(",", $new_values);
                $answer->write();
            }

            // logs
            $answers = SurveyAnswerLog::get()->filter([
                'QuestionID' => $question->ID
            ]);

            foreach($answers as $answer){
                $replaced   = false;
                $new_values = [];
                foreach (explode(",", $answer->FormerValue) as $answer_value){
                    if(in_array(intval($answer_value), $values_2_replace)){
                        if($replaced) continue;
                        $replaced = true;
                        $new_values[] = intval($new_option->ID);
                        continue;
                    }
                    $new_values[] = intval($answer_value);
                }
                $answer->FormerValue = implode(",", $new_values);
                $new_values = [];
                foreach (explode(",", $answer->NewValue) as $answer_value){
                    if(in_array(intval($answer_value), $values_2_replace)){
                        if($replaced) continue;
                        $replaced = true;
                        $new_values[] = intval($new_option->ID);
                        continue;
                    }
                    $new_values[] = intval($answer_value);
                }
                $answer->NewValue = implode(",", $new_values);
                $answer->write();
            }

            // delete former options
            foreach ($options_to_merge as $option){
                $option->delete();
            }
        }
    }

}