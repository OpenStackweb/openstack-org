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
 * Class SurveyManager
 */
final class SurveyManager implements ISurveyManager {

    /**
     * @var ISurveyRepository
     */
    private $survey_repository;

    /**
     * @var IEntityRepository
     */
    private $template_repository;


    /**
     * @var ISurveyBuilder
     */
    private $survey_builder;

    /**
     * @var IFoundationMemberRepository
     */
    private $member_repository;

    /**
     * @var ITransactionManager
     */
    private $tx_manager;

    /**
     * @var ISurveyTemplateFactory
     */
    private $template_factory;

    /**
     * SurveyManager constructor.
     * @param ISurveyRepository $survey_repository
     * @param IEntityRepository $template_repository
     * @param IFoundationMemberRepository $member_repository
     * @param ISurveyBuilder $survey_builder
     * @param ISurveyTemplateFactory $template_factory
     * @param ITransactionManager $tx_manager
     */
    public function __construct(ISurveyRepository $survey_repository,
                                IEntityRepository $template_repository,
                                IFoundationMemberRepository $member_repository,
                                ISurveyBuilder $survey_builder,
                                ISurveyTemplateFactory $template_factory,
                                ITransactionManager $tx_manager){

        $this->survey_repository   = $survey_repository;
        $this->template_repository = $template_repository;
        $this->member_repository   = $member_repository;
        $this->survey_builder      = $survey_builder;
        $this->template_factory    = $template_factory;
        $this->tx_manager          = $tx_manager;
    }

    /**
     * @param int $template_id
     * @param int $creator_id
     * @return ISurvey
     */
    public function buildSurvey($template_id, $creator_id){

        $template_repository = $this->template_repository;
        $survey_repository   = $this->survey_repository;
        $survey_builder      = $this->survey_builder;
        $member_repository   = $this->member_repository;

        return $this->tx_manager->transaction(function() use($template_id, $creator_id, $survey_builder, $member_repository, $template_repository, $survey_repository){

            $template = $template_repository->getById($template_id);

            if(is_null($template)) throw new NotFoundEntityException('SurveyTemplate','');

            $owner = $member_repository->getById($creator_id);

            if(is_null($owner)) throw new NotFoundEntityException('Member','');

            $survey = $survey_repository->getByTemplateAndCreator($template->getIdentifier(), $creator_id);

            if(is_null($survey)){
                $survey = $survey_builder->build($template, $owner);
                $survey_repository->add($survey);
            }

            return $survey;

        });
    }

    /**
     * @param ISurveyDynamicEntityStep $step
     * @param int $creator_id
     * @return IEntitySurvey
     */
    public function buildEntitySurvey(ISurveyDynamicEntityStep $step, $creator_id)
    {
        $template_repository = $this->template_repository;
        $survey_repository   = $this->survey_repository;
        $survey_builder      = $this->survey_builder;
        $member_repository   = $this->member_repository;


        return $this->tx_manager->transaction(function() use($step, $creator_id, $survey_builder, $member_repository, $template_repository, $survey_repository){

            $owner = $member_repository->getById($creator_id);

            if(is_null($owner)) throw new NotFoundEntityException('Member','');

            $entity_survey = $survey_builder->buildEntitySurvey($step->survey(), $step->template()->getEntity(), $owner);

            $step->addEntitySurvey($entity_survey);

            return $entity_survey;
        });
    }

    /**
     * @param array $data
     * @param ISurveyStep $current_step
     * @return ISurveyStep
     */
    public function completeStep(ISurveyStep $current_step, array $data)
    {

        $template_repository = $this->template_repository;
        $survey_repository   = $this->survey_repository;
        $survey_builder      = $this->survey_builder;
        $member_repository   = $this->member_repository;

        return $this->tx_manager->transaction(function() use($current_step, $data, $survey_builder, $member_repository, $template_repository, $survey_repository){

            $current_survey = $current_step->survey();

            if($current_step instanceof ISurveyRegularStep) {
                $current_step->clearAnswers();
                foreach ($current_step->template()->getQuestions() as $q)
                {
                    if (isset($data[$q->name()]))
                    {
                        // its has an answer set
                        if($q->name() === SurveyOrganizationQuestionTemplate::FieldName){
                            //publish event
                            PublisherSubscriberManager::getInstance()->publish('survey_organization_selected', array( $current_survey->createdBy(), $data[$q->name()]));
                        }
                        $current_step->addAnswer($survey_builder->buildAnswer($q, $data[$q->name()]));
                    }
                }
            }
            return $current_survey->completeCurrentStep();
        });
    }

    /**
     * @param ISurvey $survey
     * @param string $step_name
     * @return void
     */
    public function registerCurrentStep(ISurvey $survey, $step_name)
    {
        $template_repository = $this->template_repository;
        $survey_repository   = $this->survey_repository;
        $survey_builder      = $this->survey_builder;
        $member_repository   = $this->member_repository;

        return $this->tx_manager->transaction(function() use($survey, $step_name, $survey_builder, $member_repository, $template_repository, $survey_repository){
            if($survey->isAllowedStep($step_name)){
                $survey->registerCurrentStep($survey->getStep($step_name));
            }
        });
    }

    /**
     * @param IMessageSenderService $sender_service
     * @param ISurvey $survey
     * @return void
     */
    public function sendFinalStepEmail(IMessageSenderService $sender_service, ISurvey $survey)
    {
        return $this->tx_manager->transaction(function() use($sender_service, $survey){
            $survey->sentEmail($sender_service);
        });
    }

    /**
     * @param ISurvey $survey
     * @param ISurveyTemplate $template
     * @return ISurvey
     */
    public function updateSurveyWithTemplate(ISurvey $survey, ISurveyTemplate $template)
    {
        if(is_null($survey))
            throw new InvalidArgumentException('$survey is null!');
        if(is_null($template))
            throw new InvalidArgumentException('$template is null!');

        $survey_builder      = $this->survey_builder;

        return $this->tx_manager->transaction(function() use($survey, $template, $survey_builder){

            $step_templates = $template->getSteps();
            $steps          = $survey->getSteps();

            foreach($step_templates as $st){
                $found  = false;
                foreach($steps as $s){
                    $found = $s->template()->getIdentifier() === $st->getIdentifier();
                    if($found) break;
                }
                if(!$found){
                    //must add this step
                    $survey->addStep( $survey_builder->buildStep($st) );
                }
            }

            //remove steps that are not valid
            foreach($steps as $s){
                $found  = false;
                foreach($step_templates as $st){
                    $found = $s->template()->getIdentifier() === $st->getIdentifier();
                    if($found) break;
                }
                if(!$found){
                    //must remove this step from survey
                    $survey->removeStep($s);
                }
            }
            $current_step = $survey->currentStep();
            if($current_step->getIdentifier() === 0 && count($steps) > 0){
                //set the current step
                $survey->registerCurrentStep($steps[0]);
                $survey->registerAllowedMaxStep($steps[0]);
            }
            return $survey;
        });
    }

    /**
     * @param int $entity_id
     * @param ISurveyDynamicEntityStep $current_step
     * @return void
     */
    public function deleteEntitySurvey(ISurveyDynamicEntityStep $current_step, $entity_id)
    {

        return $this->tx_manager->transaction(function() use($current_step, $entity_id){

            $current_step->removeEntitySurveyById($entity_id);

        });
    }

    /**
     * @param ISurvey $survey
     * @return void
     */
    public function resetSteps(ISurvey $survey)
    {
        return $this->tx_manager->transaction(function() use($survey){

            if($survey->isLastStep()){
                $steps = $survey->getSteps();
                // reset to first step
                $survey->registerCurrentStep($steps[0]);
            }
        });
    }

    /**
     * @param int $entity_survey_id
     * @param int $member_id
     * @param IMessageSenderService $sender_service
     * @return void
     */
    public function registerTeamMemberOnEntitySurvey(
        $entity_survey_id,
        $member_id,
        IMessageSenderService $sender_service = null
    ) {
        $survey_repository   = $this->survey_repository;
        $member_repository   = $this->member_repository;

        return $this->tx_manager->transaction(function() use
        (
            $entity_survey_id,
            $member_id,
            $sender_service,
            $member_repository,
            $survey_repository
        )
        {

            $member = $member_repository->getById($member_id);

            if(is_null($member)) throw new NotFoundEntityException('Member','');

            $survey = $survey_repository->getById($entity_survey_id);

            if(is_null($survey)) throw new NotFoundEntityException('EntitySurvey','');

            if(!$survey instanceof IEntitySurvey) throw new NotFoundEntityException('EntitySurvey','');

            if($member->getIdentifier() === $survey->createdBy()->getIdentifier())
                throw new Exception('You cant add owner as a team member!');

            if($survey->isTeamMember($member))
                throw new Exception('Member already belongs to team!');

            $survey->addTeamMember($member);

            if(!is_null($sender_service))
                $sender_service->send($member);
        });
    }

    /**
     * @param int $entity_survey_id
     * @param int $member_id
     * @return void
     */
    public function unRegisterTeamMemberOnEntitySurvey($entity_survey_id, $member_id)
    {
        $survey_repository   = $this->survey_repository;
        $member_repository   = $this->member_repository;

        return $this->tx_manager->transaction(function() use
        (
            $entity_survey_id,
            $member_id,
            $member_repository,
            $survey_repository
        )
        {

            $member = $member_repository->getById($member_id);

            if(is_null($member)) throw new NotFoundEntityException('Member','');

            $survey = $survey_repository->getById($entity_survey_id);

            if(is_null($survey)) throw new NotFoundEntityException('EntitySurvey','');

            if(!$survey instanceof IEntitySurvey) throw new NotFoundEntityException('EntitySurvey','');

            if($member->getIdentifier() === $survey->createdBy()->getIdentifier())
                throw new Exception('You cant add owner as a team member!');

            $survey->removeTeamMember($member);

        });
    }

    /**
     * @param ISurvey $survey
     * @param ISurveyAutopopulationStrategy $strategy
     * @return mixed
     */
    public function doAutopopulation(ISurvey $survey, ISurveyAutopopulationStrategy $strategy)
    {
        $survey_builder = $this->survey_builder;
        $this_var      = $this;
        $this->tx_manager->transaction(function() use($survey, $strategy, $survey_builder, $this_var)
        {
            $strategy->autoPopulate($survey, $survey_builder, $this_var);
        });
    }

    /**
     * @param ISurveyTemplate $template
     * @param null $clone_name
     * @param null $parent_id
     * @return mixed
     */
    public function doClone(ISurveyTemplate $template, $clone_name = null, $parent_id = null)
    {
        $template_repository = $this->template_repository;
        $template_factory    = $this->template_factory;

        return $this->tx_manager->transaction(function() use($template, $clone_name, $template_repository, $template_factory, $parent_id)
        {
            $now = new \DateTime();
            if(empty($clone_name)) $clone_name = $template->getTitle().'-Clone-'.$now->format('Y-m-d-H-i-s');
            $template_clone        = $template_factory->cloneTemplate($template, $parent_id);
            $template_clone->Title = $clone_name;
            $template_clone->write();
            $original_clone_questions_dictionary       = array();
            $original_clone_question_values_dictionary = array();
            foreach($template->getSteps() as $original_step)
            {
                $clone_step = $template_factory->cloneStep($original_step);
                $clone_step->SurveyTemplateID = $template_clone->ID;
                $cloned_entity                = null;
                if($clone_step instanceof SurveyDynamicEntityStepTemplate)
                {
                    // create entity
                    $original_entity         = $original_step->getEntity();
                    $cloned_entity           = $this->doClone($original_entity, $original_entity->getTitle(), $template_clone->ID);
                    $cloned_entity->ParentID = $template_clone->ID;
                    $cloned_entity->OwnerID  = 0;
                    $cloned_entity->write();
                    $clone_step->EntityID    = $cloned_entity->ID;
                }
                $clone_step->write();
                foreach($original_step->getDependsOn() as $original_step_dependant)
                {
                    if(!isset($original_clone_questions_dictionary[$original_step_dependant->ID])) continue;
                    if(!isset($original_clone_question_values_dictionary[$original_step_dependant->ValueID])) continue;
                    $new_id           = $original_clone_questions_dictionary[$original_step_dependant->ID];
                    $value_id         = $original_clone_question_values_dictionary[$original_step_dependant->ValueID];
                    $operator         = $original_step_dependant->Operator;
                    $visibility       = $original_step_dependant->Visibility;
                    $boolean_operator = $original_step_dependant->BooleanOperatorOnValues;

                    DB::query("INSERT INTO SurveyStepTemplate_DependsOn
                    (SurveyStepTemplateID, SurveyQuestionTemplateID , ValueID, Operator, Visibility, BooleanOperatorOnValues)
                    VALUES ({$clone_step->ID}, {$new_id}, $value_id,'{$operator}','{$visibility}','{$boolean_operator}');");
                }
                if($clone_step instanceof SurveyRegularStepTemplate) {
                    // create questions
                    foreach ($original_step->getQuestions() as $original_question) {
                        $clone_question         = $template_factory->cloneQuestion($original_question);
                        $clone_question->StepID = $clone_step->ID;
                        $clone_question->write();
                        $original_clone_questions_dictionary[$original_question->ID] = $clone_question->ID;
                        foreach($original_question->getDependsOn() as $original_dependant_question)
                        {
                            if(!isset($original_clone_questions_dictionary[$original_dependant_question->ID])) continue;
                            if(!isset($original_clone_question_values_dictionary[$original_dependant_question->ValueID])) continue;
                            $new_id           = $original_clone_questions_dictionary[$original_dependant_question->ID];
                            $value_id         = $original_clone_question_values_dictionary[$original_dependant_question->ValueID];
                            $operator         = $original_dependant_question->Operator;
                            $visibility       = $original_dependant_question->Visibility;
                            $initial_value    = $original_dependant_question->DependantDefaultValue;
                            $boolean_operator = $original_dependant_question->BooleanOperatorOnValues;
                            DB::query("INSERT INTO SurveyQuestionTemplate_DependsOn
                            (SurveyQuestionTemplateID, ChildID , ValueID,Operator, Visibility, DefaultValue, BooleanOperatorOnValues)
                            VALUES ({$clone_question->ID}, {$new_id}, $value_id,'{$operator}','{$visibility}','{$initial_value}', '{$boolean_operator}');");
                        }
                        if($original_question instanceof IMultiValueQuestionTemplate)
                        {
                            if($original_question instanceof IDropDownQuestionTemplate && $original_question->isCountrySelector()) continue;
                            foreach($original_question->getValues() as $original_val)
                            {
                                $clone_val = $template_factory->cloneQuestionValue($original_val);
                                $clone_val->OwnerID = $clone_question->ID;
                                $clone_val->write();
                                $original_clone_question_values_dictionary[$original_val->ID] = $clone_val->ID;
                            }
                        }
                        if($original_question instanceof IDoubleEntryTableQuestionTemplate)
                        {
                            foreach($original_question->getColumns() as $original_val)
                            {
                                $clone_val = $template_factory->cloneQuestionValue($original_val);
                                $clone_val->OwnerID = $clone_question->ID;
                                $clone_val->write();
                                $original_clone_question_values_dictionary[$original_val->ID] = $clone_val->ID;
                            }
                            foreach($original_question->getRows() as $original_val)
                            {
                                $clone_val = $template_factory->cloneQuestionValue($original_val);
                                $clone_val->OwnerID = $clone_question->ID;
                                $clone_val->write();
                                $original_clone_question_values_dictionary[$original_val->ID] = $clone_val->ID;
                            }
                            foreach($original_question->getAlternativeRows() as $original_val)
                            {
                                $clone_val = $template_factory->cloneQuestionValue($original_val);
                                $clone_val->OwnerID = $clone_question->ID;
                                $clone_val->write();
                                $original_clone_question_values_dictionary[$original_val->ID] = $clone_val->ID;
                            }
                        }
                    }
                }
                if($clone_step instanceof SurveyThankYouStepTemplate) {
                   // clone email ?
                }
                if($clone_step instanceof SurveyDynamicEntityStepTemplate && !is_null($cloned_entity))
                {
                    $cloned_entity->OwnerID = $clone_step->ID;
                    $cloned_entity->write();
                    $cloned_entity = null;
                }
            }
            return $template_clone;
        });
    }
}