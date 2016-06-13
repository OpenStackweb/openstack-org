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
 * Class AbstractRSVPQuestionTemplateUIBuilder
 */
abstract class AbstractRSVPQuestionTemplateUIBuilder
    implements IRSVPQuestionTemplateUIBuilder {

    /**
     * @param IRSVP $rsvp
     * @param IRSVPQuestionTemplate $question
     * @param FormField $field
     */
    protected function buildDependantRules(IRSVP $rsvp, IRSVPQuestionTemplate $question, FormField $field){
        //depends : check visibility
        $depends = $question->getDependsOn();

        if(count($depends) > 0){

            $js_rules     = array();
            $static_rules = array();

            // pre process ...
            foreach($depends as $d) {
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


            $static_strategy  = new StaticRulesStrategy;
            $dynamic_strategy = new JSRulesStrategy;

            $static_strategy->apply($rsvp, $question, $static_rules, $field);
            $dynamic_strategy->apply($rsvp, $question, $js_rules, $field);

        }
    }
}

/**
 * Interface IRSVPDependantRulesStrategy
 */
interface IRSVPDependantRulesStrategy {
    /**
     * @param IRSVP $rsvp
     * @param IRSVPQuestionTemplate $question
     * @param array $rules
     * @param FormField $field
     * @return void
     */
    public function apply( IRSVP $rsvp ,IRSVPQuestionTemplate $question, array $rules, FormField $field );
}

/**
 * Class RSVPStaticRulesStrategy
 */
final class RSVPStaticRulesStrategy implements IRSVPDependantRulesStrategy {

    /**
     * @param IRSVP $rsvp
     * @param IRSVPQuestionTemplate $question
     * @param array $static_rules
     * @param FormField $field
     */
    public function apply(IRSVP $rsvp ,IRSVPQuestionTemplate $question, array $static_rules, FormField $field ){

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

                $answer = $rsvp->findAnswerByQuestion($q);
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
                    if($question instanceof IRSVPMultiValueQuestionTemplate)
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
 * Class RSVPJSRulesStrategy
 */
final class RSVPJSRulesStrategy implements IRSVPDependantRulesStrategy {

    /**
     * @param IRSVP $rsvp
     * @param IRSVPQuestionTemplate $question
     * @param array $js_rules
     * @param FormField $field
     */
    public function apply( IRSVP $rsvp ,IRSVPQuestionTemplate $question, array $js_rules, FormField $field ){

        if(count($js_rules)) {

            $js = "jQuery(document).ready(function($){

                    var form                = $('.rsvp_form');
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

                if ($d instanceof IRSVPClickableQuestion) {
                    foreach($values as $value)
                    {
                        $option_id = $d->name();

                        if ($d instanceof IRSVPMultiValueQuestionTemplate && intval($value) > 0) {
                            $option_id .= '_' . str_replace(' ', '', intval($value));
                        }
                        $js .= " clickable_fields.push($('#'+form_id+'_{$option_id}'));";
                    }
                }

                if ( ($d instanceof IRSVPSelectableQuestion) && ($d instanceof IRSVPMultiValueQuestionTemplate))
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

            Requirements::customScript($js);
        }
    }
}