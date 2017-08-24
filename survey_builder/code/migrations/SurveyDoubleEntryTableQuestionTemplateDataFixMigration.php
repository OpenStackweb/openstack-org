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
 * Class SurveyDoubleEntryTableQuestionTemplateDataFixMigration
 */
final class SurveyDoubleEntryTableQuestionTemplateDataFixMigration extends AbstractDBMigrationTask
{
    protected $title = "SurveyDoubleEntryTableQuestionTemplateDataFixMigration";

    protected $description = "SurveyDoubleEntryTableQuestionTemplateDataFixMigration";

    function doUp()
    {
        global $database;

        $current_template_id = 9;
        $former_template_id  = 7;

        $questions_names = ['ProjectsUsed', 'ProjectsUsedPoC'];

        $query_question_template = <<<SQL
SELECT Q.ID from SurveyQuestionTemplate  Q
INNER JOIN SurveyStepTemplate STPL ON STPL.ID = Q.StepID
INNER JOIN SurveyTemplate SSTPL ON SSTPL.ID = STPL.SurveyTemplateID
WHERE SSTPL.ID = %s AND Q.Name = '%s';
SQL;

        $current_label_template_id = DB::query(sprintf($query_question_template, $current_template_id, 'Label'))->value();
        $former_label_template_id  = DB::query(sprintf($query_question_template, $former_template_id, 'Label'))->value();

        $questions = [];
        foreach ($questions_names as $question_name){
            $current_id = DB::query(sprintf($query_question_template, $current_template_id, $question_name))->value();
            $former_id  = DB::query(sprintf($query_question_template, $former_template_id, $question_name))->value();
            $questions[$current_id] = $former_id;
        }

        foreach($questions as $current_id => $former_id) {
            echo sprintf("processing current_question_template_id %s former_question_template_id %s", $current_id, $former_id).PHP_EOL;
            $count     = 0;
            foreach (DB::query("SELECT A.*, STPL.ID AS SurveyStepTemplateID, I.ID AS SurveyID, I.CreatedByID, Q.Name  
FROM SurveyAnswer A 
INNER JOIN SurveyQuestionTemplate Q ON Q.ID = A.QuestionID
INNER JOIN SurveyStepTemplate STPL ON STPL.ID = Q.StepID
INNER JOIN SurveyTemplate SSTPL ON SSTPL.ID = STPL.SurveyTemplateID
INNER JOIN SurveyStep S ON S.ID = A.StepIDROM
INNER JOIN Survey I ON I.ID = S.SurveyID
WHERE 
A.QuestionID = {$current_id}
AND SSTPL.ID = {$current_template_id}
AND A.Value is not null
AND A.Value NOT LIKE '%:%';") as $row) {

                $answer_id = $row["ID"];
                $member_id = $row['CreatedByID'];
                $survey_id = $row['SurveyID'];

                $label_value = DB::query("SELECT A.Value 
FROM SurveyAnswer A 
INNER JOIN SurveyQuestionTemplate Q ON Q.ID = A.QuestionID
INNER JOIN SurveyStepTemplate STPL ON STPL.ID = Q.StepID
INNER JOIN SurveyTemplate SSTPL ON SSTPL.ID = STPL.SurveyTemplateID
INNER JOIN SurveyStep S ON S.ID = A.StepID
INNER JOIN Survey I ON I.ID = S.SurveyID
WHERE 
A.QuestionID = {$current_label_template_id} 
AND A.Value is not null
AND I.CreatedByID = {$member_id}
AND I.ID = {$survey_id} LIMIT 0,1;")->value();
                if (empty($label_value)) continue;

                $former_value = DB::query("SELECT A.Value
FROM SurveyAnswer A 
INNER JOIN SurveyQuestionTemplate Q ON Q.ID = A.QuestionID
INNER JOIN SurveyStepTemplate STPL ON STPL.ID = Q.StepID
INNER JOIN SurveyTemplate SSTPL ON SSTPL.ID = STPL.SurveyTemplateID
INNER JOIN SurveyStep S ON S.ID = A.StepID
INNER JOIN Survey I ON I.ID = S.SurveyID
WHERE 
A.QuestionID = {$former_id} 
AND A.Value is not null
AND I.CreatedByID = {$member_id}
AND EXISTS(
SELECT A2.ID FROM SurveyAnswer A2
WHERE 
A2.QuestionID = {$former_label_template_id}
AND A2.Value = '{$label_value}' 
AND A2.StepID = A.StepID 
)")->value();

                if (empty($former_value)) continue;

                $new_tuples = [];

                foreach (explode(',', $former_value) as $tuple) {
                    list($row_id, $col_id) = explode(':', $tuple);
                    $col     = SurveyQuestionColumnValueTemplate::get()->byID($col_id);
                    if(is_null($col)) continue;
                    $row     = SurveyQuestionRowValueTemplate::get()->byID($row_id);
                    if(is_null($row)) continue;
                    $col_val = $col->Value;
                    $row_val = $row->Value;
                    echo sprintf("looking for col value %s on current question template id %s", $col_val, $current_id).PHP_EOL;
                    $new_col = SurveyQuestionColumnValueTemplate::get()->filter(
                        [
                            'Value'   => $col_val,
                            'OwnerID' => $current_id
                        ]
                    )->first();
                    $new_row = SurveyQuestionRowValueTemplate::get()->filter(
                        [
                            'Value'   => $row_val,
                            'OwnerID' => $current_id
                        ]
                    )->first();
                    echo sprintf("looking for row value %s on current question template id %s", $row_val, $current_id).PHP_EOL;
                    if (is_null($new_col) || is_null($new_row)) continue;
                    echo sprintf("col/row found on current question template id %s", $current_id).PHP_EOL;
                    $new_tuples[] = "{$new_row->ID}:{$new_col->ID}";
                }

                $answer = SurveyAnswer::get()->byID($answer_id);

                if (is_null($answer)) continue;

                $answer->Value = implode(',', $new_tuples);

                echo sprintf("new value %s for answer id %s", $answer->Value, $answer->ID) . PHP_EOL;
                $answer->write();
                ++$count;
            }
            echo sprintf("processed %s answers", $count).PHP_EOL;
        }
    }

    function doDown()
    {

    }
}