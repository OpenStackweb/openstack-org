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
 * Class SapphireSurveyRepository
 */
class SapphireSurveyRepository
    extends SapphireRepository
    implements ISurveyRepository {

    public function __construct(){
        parent::__construct(new Survey());
    }

    /**
     * @param int $template_id
     * @param int $creator_id
     * @return ISurvey|null
     */
    public function getByTemplateAndCreator($template_id, $creator_id)
    {
       $query = new QueryObject();
       $query->addAndCondition(QueryCriteria::equal('TemplateID', $template_id));
       $query->addAndCondition(QueryCriteria::equal('CreatedByID', $creator_id));
       return $this->getBy($query);
    }

    /**
     * @param $template_id
     * @param $question_id
     * @param array $values
     * @param PagingInfo $page_info
     * @param Order|null $order
     * @param string $survey_lang
     * @return array
     */
    public function getByTemplateAndAnswerValue($template_id, $question_id , array $values = [], PagingInfo $page_info, Order $order = null, $survey_lang = 'ALL'){
$from_query = <<<SQL
FROM Survey 
INNER JOIN SurveyStep 
ON SurveyStep.SurveyID = Survey.ID 
INNER JOIN SurveyAnswer ON SurveyAnswer.StepID = SurveyStep.ID 
INNER JOIN SurveyQuestionTemplate ON SurveyQuestionTemplate.ID = SurveyAnswer.QuestionID 
WHERE
Survey.TemplateID = {$template_id}
AND Survey.IsTest = 0 
SQL;


        if($question_id > 0 ){
            $from_query .= "AND SurveyQuestionTemplate.ID = {$question_id} ";
        }
        if($survey_lang != 'ALL'){
            $from_query .= "AND Survey.Lang = '{$survey_lang}'";
        }

        $question_values_condition = "";
        foreach($values as $question_val){
            if(!empty($question_values_condition)) $question_values_condition .= " OR ";
            $question_values_condition .= "SurveyAnswer.Value LIKE '%{$question_val}%' ";
        }

        if(!empty($question_values_condition)) $question_values_condition = "AND ({$question_values_condition})";

        $from_query.= $question_values_condition;

        $count             = intval(DB::query("SELECT COUNT(DISTINCT Survey.ID) {$from_query}")->value());
        $count_completed   = intval(DB::query("SELECT COUNT(DISTINCT Survey.ID) {$from_query} AND Survey.State = 'COMPLETE' ")->value());
        $count_deployments = intval(DB::query("SELECT COUNT(DISTINCT EntitySurvey.ID) FROM EntitySurvey WHERE ParentID IN ( SELECT DISTINCT Survey.ID {$from_query})")->value());
        $order_by          = '';

        if(!is_null($order)){
            $order_by= $order->toRawSQL([
                'id'      => 'Survey.ID',
                'created' => 'Survey.Created',
                'updated' => 'Survey.LastEdited'
            ]);
        }

        $query  = "SELECT DISTINCT Survey.* {$from_query} {$order_by} LIMIT {$page_info->getOffset()}, {$page_info->getPerPage()}";

        $list = new ArrayList();
        foreach(DB::query($query) as $row){
            $list->push(new Survey($row));
        }

        return [$list, $count, $count_completed, $count_deployments];
    }
}