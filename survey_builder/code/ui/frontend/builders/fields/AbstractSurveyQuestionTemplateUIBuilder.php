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
 * Class AbstractSurveyQuestionTemplateUIBuilder
 */
abstract class AbstractSurveyQuestionTemplateUIBuilder
    implements ISurveyQuestionTemplateUIBuilder {

    /**
     * @param ISurveyStep $current_step
     * @param ISurveyQuestionTemplate $question
     * @param FormField $field
     */
    protected function buildDependantRules(ISurveyStep $current_step, ISurveyQuestionTemplate $question, FormField $field){
        //depends : check visibility
        $depends = $question->getDependsOn();
        //$depends = $question->DependsOn();
        if(count($depends) > 0){

            $js_rules     = array();
            $static_rules = array();

            // pre process ...
            foreach($depends as $d) {
                 if($question->step()->belongsToQuestion($d)){
                     // js rule, question on which we depends on its on the same step (form)
                     if(!isset( $js_rules[$d->getIdentifier()]))
                         $js_rules[$d->getIdentifier()] = array (
                             'question'          => $d,
                             'values'            => array(),
                             'operator'          => $d->Operator,
                             'visibility'        => $d->Visibility,
                             'default'           => $d->DependantDefaultValue,
                             'boolean_operator'  => $d->BooleanOperatorOnValues,
                             'initial_condition' => ($d->BooleanOperatorOnValues === 'And') ? true:false
                         );

                     array_push($js_rules[$d->getIdentifier()]['values'], $d->ValueID);
                 }
                 else{
                     // belongs to another step (former one)
                     if(!isset($static_rules[$d->getIdentifier()]))
                         $static_rules[$d->getIdentifier()] = array (
                            'question'           => $d,
                            'values'             => [],
                            'operator'           => $d->Operator,
                            'visibility'         => $d->Visibility,
                            'default'            => $d->DependantDefaultValue,
                            'boolean_operator'   => $d->BooleanOperatorOnValues,
                            'initial_condition'  => ($d->BooleanOperatorOnValues === 'And') ? true:false
                         );

                     array_push($static_rules[$d->getIdentifier()]['values'], $d->ValueID);
                 }
            }


            $static_strategy  = new StaticRulesStrategy;
            $dynamic_strategy = new JSRulesStrategy;

            $static_strategy->apply($current_step, $question, $static_rules, $field);
            $dynamic_strategy->apply($current_step, $question, $js_rules, $field);

        }
    }
}

/**
 * Interface IDependantRulesStrategy
 */
interface IDependantRulesStrategy {
    /**
     * @param ISurveyStep $current_step
     * @param ISurveyQuestionTemplate $question
     * @param array $rules
     * @param FormField $field
     * @return void
     */
    public function apply( ISurveyStep $current_step ,ISurveyQuestionTemplate $question, array $rules, FormField $field );
}

/**
 * Class StaticRulesStrategy
 */
final class StaticRulesStrategy implements IDependantRulesStrategy {

    /**
     * @param ISurveyStep $current_step
     * @param ISurveyQuestionTemplate $question
     * @param array $static_rules
     * @param FormField $field
     */
    public function apply(ISurveyStep $current_step ,ISurveyQuestionTemplate $question, array $static_rules, FormField $field ){

        if(count($static_rules)){

            foreach ($static_rules as $id => $info)
            {

                $q                 = $info['question'];
                $values            = $info['values'];
                $operator          = $info['operator'];
                $visibility        = $info['visibility'];
                $default           = $info['default'];
                $boolean_operator  = $info['boolean_operator'];
                $initial_condition = $info['initial_condition'];

                $answer = $current_step->survey()->findAnswerByQuestion($q);
                if(is_null($answer)) continue;

                //checks the condition
                switch($operator){
                    case 'Equal':{
                        foreach($values as $vid) {
                            if($boolean_operator === 'And')
                                $initial_condition &= (strpos($answer->value(), $vid) !== false);
                            else
                                $initial_condition |= (strpos($answer->value(), $vid) !== false);
                        }
                    }
                    break;
                    case 'Not-Equal':{
                        foreach($values as $vid) {
                            if($boolean_operator === 'And')
                                $initial_condition &= (strpos($answer->value(), $vid) === false);
                            else
                                $initial_condition |= (strpos($answer->value(), $vid) === false);
                        }
                    }
                    break;
                }
                //visibility
                switch($visibility)
                {
                    case 'Visible':
                    {
                        if(!$initial_condition){
                            $field->addExtraClass('hidden');
                            // if not visible clean it
                            $field->setValue('');
                        }
                        else
                        {
                            $field->removeExtraClass('hidden');
                        }
                    }
                    break;
                    case 'Not-Visible':{
                        if($initial_condition) {
                            $field->addExtraClass('hidden');
                            // if not visible clean it
                            $field->setValue('');
                        }
                        else
                        {
                            $field->removeExtraClass('hidden');
                        }
                    }
                    break;
                }

                // set the default value set on the rule
                if(!empty($default))
                {
                    $field->setValue($default);
                    if($question instanceof IMultiValueQuestionTemplate)
                    {
                        $value_template = $question->getValueByValue($default);
                        if(!is_null($value_template)) $field->setValue($value_template->getIdentifier());
                    }
                }
            }
        }
    }
}

/**
 * Class JSRulesStrategy
 */
final class JSRulesStrategy implements IDependantRulesStrategy {

    /**
     * @param ISurveyStep $current_step
     * @param ISurveyQuestionTemplate $question
     * @param array $js_rules
     * @param FormField $field
     */
    public function apply(ISurveyStep $current_step ,ISurveyQuestionTemplate $question, array $js_rules, FormField $field ){

        if(count($js_rules)) {

            $js = "jQuery(document).ready(function($){

                    var form                = $('.survey_step_form');
                    var form_id             = form.attr('id');
                    var clickable_fields    = [];
                    var selectable_fields   = [];
                    var rankable_fields     = [];
                    var double_table_fields = [];
                    ";

            //hide and set js rule
            $field->addExtraClass('hidden');
            $question_id = $question->name();

            foreach ($js_rules as $id => $info) {

                $d          = $info['question'];
                $values     = $info['values'];
                $operator   = $info['operator'];
                $visibility = $info['visibility'];

                if
                (
                    ($d instanceof ISurveyClickableQuestion) ||
                    ($d instanceof ISurveyRankableQuestion)  ||
                    ($d instanceof IDoubleEntryTableQuestionTemplate)
                )
                {
                    foreach($values as $value)
                    {
                        $option_id = $d->name();

                        if ($d instanceof ISurveyClickableQuestion)
                        {
                            if ($d instanceof IMultiValueQuestionTemplate && intval($value) > 0) {
                                //$value = $d->getValueById(intval($d->ValueID));
                                $option_id .= '_' . str_replace(' ', '', intval($value));
                            }
                            $js .= " clickable_fields.push($('#'+form_id+'_{$option_id}'));";
                        }

                        if ($d instanceof ISurveyRankableQuestion)
                        {
                            if ($d instanceof IMultiValueQuestionTemplate && intval($value) > 0) {
                                if ($d instanceof IMultiValueQuestionTemplate && intval($value) > 0) {
                                    //$value = $d->getValueById(intval($d->ValueID));
                                    $option_id .= '_' . str_replace(' ', '', intval($value));
                                }
                                $js .= " rankable_fields.push( $('#'+form_id+'_{$option_id}') );";
                            }
                        }

                        if($d instanceof IDoubleEntryTableQuestionTemplate)
                        {
                            $js .= " double_table_fields.push({table : $('#'+'{$option_id}'), value: $value });";
                        }
                    }
                }

                if ( ($d instanceof ISurveySelectableQuestion) && ($d instanceof IMultiValueQuestionTemplate))
                {
                    $option_id = $d->name();
                    $values = implode(',',$values);
                    $js .= " selectable_fields.push({ddl : $('#'+form_id+'_{$option_id}'), values: [{$values}] });";
                }
            }

            $js .= "for(var i = 0 ; i < selectable_fields.length; i++ ){
                form.survey_validation_rules('addRequiredAnswer4SelectAbleGroup', selectable_fields, $('#{$question_id}'));
                }";

            $js .= "if(clickable_fields.length > 0 )
                form.survey_validation_rules('addRequiredAnswer4CheckAbleGroup', clickable_fields, $('#{$question_id}') ); ";

            $js .= "if(rankable_fields.length > 0 )
                form.survey_validation_rules('addRequiredAnswer4RankAbleGroup', rankable_fields, $('#{$question_id}') );";

            $js .= "if(double_table_fields.length > 0 )
                form.survey_validation_rules('addRequiredAnswer4TableGroup', double_table_fields, $('#{$question_id}') );
                });";

            Requirements::customScript($js);
        }
    }
}