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
     * @param string|null $lang
     * @return ISurvey
     */
    public function buildSurvey($template_id, $creator_id, $lang = null){


        return $this->tx_manager->transaction(function() use($template_id, $creator_id, $lang){

            $template = $this->template_repository->getById($template_id);

            if(is_null($template)) throw new NotFoundEntityException('SurveyTemplate','');

            $owner = $this->member_repository->getById($creator_id);

            if(is_null($owner)) throw new NotFoundEntityException('Member','');

            $survey = $this->survey_repository->getByTemplateAndCreator($template->getIdentifier(), $creator_id);

            if(is_null($survey)){
                $survey = $this->survey_builder->build($template, $owner);
                $this->survey_repository->add($survey);
            }
            if(is_null($lang)) $lang = GetText::current_locale();
            $survey->Lang = $lang;

            return $survey;

        });
    }

    /**
     * @param ISurveyDynamicEntityStep $step
     * @param int $creator_id
     * @param string|null $lang
     * @return IEntitySurvey
     */
    public function buildEntitySurvey(ISurveyDynamicEntityStep $step, $creator_id, $lang = null)
    {

        return $this->tx_manager->transaction(function() use($step, $creator_id, $lang){

            $owner = $this->member_repository->getById($creator_id);

            if(is_null($owner)) throw new NotFoundEntityException('Member','');

            $entity_survey = $this->survey_builder->buildEntitySurvey($step->survey(), $step->template()->getEntity(), $owner);

            $step->addEntitySurvey($entity_survey);
            $step->markComplete();
            if(is_null($lang)) $lang = GetText::current_locale();
            $entity_survey->Lang = $lang;
            return $entity_survey;
        });
    }

    /**
     * @param array $answers
     * @param ISurveyStep $current_step
     * @param string|null $lang
     * @return ISurveyStep
     */
    public function completeStep(ISurveyStep $current_step, array $answers, $lang = null)
    {
        return $this->tx_manager->transaction(function() use($current_step, $answers, $lang){

            $current_survey          = $current_step->survey();
            if(is_null($lang)) $lang = GetText::current_locale();
            $current_survey->Lang = $lang;
            $current_survey->write();
            $save_later           = isset($answers['SAVE_LATER']) && intval($answers['SAVE_LATER']) === 1;

            if($current_step instanceof ISurveyRegularStep) {
                $snapshot = $current_step->getCurrentAnswersSnapshotState();

                $current_step->clearAnswers();

                foreach ($current_step->template()->getQuestions() as $q)
                {
                    $question_name = $q->name();

                    // answer changes
                    $log             = new SurveyAnswerLog();
                    $log->QuestionID = $q->ID;
                    $log->StepID     = $current_step->ID;
                    $log->SurveyID   = $current_step->survey()->ID;
                    $log->MemberID   = Member::currentUserID();

                    if(isset($snapshot[$question_name])){
                        $log->FormerValue = $snapshot[$question_name];
                    }

                    if (isset($answers[$question_name]) && !empty($answers[$question_name]) && $q->isValidAnswerValue($answers[$question_name]))
                    {
                        $answer_val = $answers[$question_name];
                        // its has an answer set
                        if($question_name === SurveyOrganizationQuestionTemplate::FieldName){
                            //publish event
                            PublisherSubscriberManager::getInstance()->publish
                            (
                                'survey_organization_selected',
                                [ $current_survey->createdBy(), $answer_val ]
                            );
                        }

                        $answer        = $this->survey_builder->buildAnswer($q, $answer_val);
                        $log->NewValue = $answer->value();
                        $current_step->addAnswer($answer);
                    }

                    if(empty($log->FormerValue) && !empty($log->NewValue)){
                        // insert of value
                        $log->Operation   = 'INSERT';
                    }

                    if(!empty($log->FormerValue) && empty($log->NewValue)){
                        // delete of value
                        $log->Operation   = 'DELETE';
                    }

                    if(!empty($log->FormerValue) && !empty($log->NewValue) && $log->FormerValue != $log->NewValue){
                        // update of value
                        $log->Operation   = 'UPDATE';
                    }

                    if(!empty($log->Operation)  && (!empty($log->NewValue) || !empty($log->FormerValue)))
                        $log->write();
                }

                if(count($current_step->getAnswers()) > 0)
                    $current_step->markComplete();
            }

            return $save_later ? $current_step : $current_survey->completeCurrentStep() ;
        });
    }

    /**
     * @param ISurveyStep $current_step
     * @return void
     */
    public function completeSurvey(ISurveyStep $current_step){
        $this->tx_manager->transaction(function() use($current_step){
            $current_survey = $current_step->survey();
            $current_step->markComplete();
            $current_step->write();
            $current_survey->markComplete();
            $current_survey->write();
        });
    }

    /**
     * @param ISurvey $survey
     * @param string $step_name
     * @return void
     */
    public function registerCurrentStep(ISurvey $survey, $step_name)
    {
        return $this->tx_manager->transaction(function() use($survey, $step_name){
            $survey->registerCurrentStep($survey->getStep($step_name));
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

            // send email to admin
            $survey_url = Director::absoluteBaseUrl().'sangria/SurveyDetails/'.$survey->ID;
            $body = 'Respondent: '.$survey->CreatedBy()->getName().'<br/>';
            $body .= 'Template: '.$survey->Template()->Title.'<br/>';
            $body .= 'State: '.$survey->State.'<br/>';
            $body .= 'Lang: '.$survey->Lang.'<br/>';
            $body .= '<a href="'.$survey_url.'">Review</a>';
            $email = EmailFactory::getInstance()->buildEmail
            (
                "noreply@openstack.org",
                SURVEY_COMPLETE_EMAIL_TO,
                "New Survey Submitted",
                $body
            );

            $email->send();

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


        return $this->tx_manager->transaction(function() use($survey, $template){

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
                    $survey->addStep($this->survey_builder->buildStep($st) );
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
     * @return int
     */
    public function deleteEntitySurvey(ISurveyDynamicEntityStep $current_step, $entity_id)
    {

        return $this->tx_manager->transaction(function() use($current_step, $entity_id){

            $current_step->removeEntitySurveyById($entity_id);

            $remain = count($current_step->getEntitySurveys());

            if($remain == 0){
                $current_step->markIncomplete();
            }

            return $remain;
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

        return $this->tx_manager->transaction(function() use
        (
            $entity_survey_id,
            $member_id,
            $sender_service
        )
        {

            $member = $this->member_repository->getById($member_id);

            if(is_null($member)) throw new NotFoundEntityException('Member','');

            $survey = $this->survey_repository->getById($entity_survey_id);

            if(is_null($survey)) throw new NotFoundEntityException('EntitySurvey','');

            if(!$survey instanceof IEntitySurvey) throw new NotFoundEntityException('EntitySurvey','');

            if(!$survey->hasTeamPermissions(Member::currentUser()))
                throw new NotFoundEntityException('EntitySurvey','');

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

        return $this->tx_manager->transaction(function() use
        (
            $entity_survey_id,
            $member_id
        )
        {

            $member = $this->member_repository->getById($member_id);

            if(is_null($member)) throw new NotFoundEntityException('Member','');

            $survey = $this->survey_repository->getById($entity_survey_id);

            if(is_null($survey)) throw new NotFoundEntityException('EntitySurvey','');

            if(!$survey->hasTeamPermissions(Member::currentUser()))
                throw new NotFoundEntityException('EntitySurvey','');

            if(!$survey instanceof IEntitySurvey) throw new NotFoundEntityException('EntitySurvey','');

            if($member->getIdentifier() === $survey->createdBy()->getIdentifier())
                throw new Exception('You cant remove owner as a team member!');

            $survey->removeTeamMember($member);

        });
    }

    public function emailTeamMembersOnEntitySurvey($entity_survey_id, IMessageSenderService $sender_service ){

        return $this->tx_manager->transaction(function() use($entity_survey_id, $sender_service){

            $survey = $this->survey_repository->getById($entity_survey_id);

            if(is_null($survey)) throw new NotFoundEntityException('EntitySurvey','');

            if(!$survey->hasTeamPermissions(Member::currentUser()))
                throw new NotFoundEntityException('EntitySurvey','');

            if(!$survey instanceof IEntitySurvey) throw new NotFoundEntityException('EntitySurvey','');

            foreach($survey->EditorTeam() as $member)
            {
                if($member->EntitySurveyTeamMemberMailed) continue;
                if(!is_null($sender_service))
                    $sender_service->send($member);

                $survey->EditorTeam()->add($member, ['EntitySurveyTeamMemberMailed' => 1]);
            }

        });
    }

    /**
     * @param ISurvey $survey
     * @param ISurveyAutopopulationStrategy $strategy
     * @return mixed
     */
    public function doAutopopulation(ISurvey $survey, ISurveyAutopopulationStrategy $strategy)
    {
        $this->tx_manager->transaction(function() use($survey, $strategy)
        {
            $strategy->autoPopulate($survey, $this->survey_builder, $this);
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
                        else if($original_question instanceof IMultiValueQuestionTemplate)
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

    /**
     * @param array $surveys_2_merge
     * @param string $merge_result_survey_title
     * @return void
     */
    public function mergeSurveys(array $surveys_2_merge, $merge_result_survey_title = "Total")
    {
        $this->tx_manager->transaction(function() use($surveys_2_merge, $merge_result_survey_title){

            $first_id = $surveys_2_merge[0];
            $last_id  = end($surveys_2_merge);
            reset($surveys_2_merge);

            $first_template = SurveyTemplate::get()->byID($first_id);
            $last_template  = SurveyTemplate::get()->byID($last_id);

            $new_template            = $this->doClone($last_template, $merge_result_survey_title);
            $new_template->StartDate = $first_template->StartDate;
            $new_template->EndDate   = $last_template->EndDate;
            $new_template->write();

            $query_ids = implode(",", $surveys_2_merge);
            $surveys_instances = Survey::get()->filter(
                [
                    'IsTest' => 0,
                ]
            )->where("TemplateID IN ({$query_ids})")->sort(["CreatedByID" =>  "ASC", "TemplateID" => "ASC"]);

            $last_user_id = null;
            $user_surveys = [];

            foreach($surveys_instances as $survey_instance){
                $current_user_id = intval($survey_instance->CreatedByID);
                if(!is_null($last_user_id) && $last_user_id != $current_user_id && count($user_surveys) > 0){
                    echo sprintf("processing %s surveys for member id %s", count($user_surveys), $last_user_id).PHP_EOL;
                    $this->processMerge($new_template, $last_user_id, $user_surveys);
                    // resets surveys per user
                    $user_surveys = [];
                }
                $last_user_id   = $current_user_id;
                $user_surveys[] = $survey_instance;
            }
            if(count($user_surveys) > 0){
                echo sprintf("processing %s surveys for member id %s", count($user_surveys), $last_user_id).PHP_EOL;
                $this->processMerge($new_template, $last_user_id, $user_surveys);
            }
        });
    }

    /**
     * @param $new_template
     * @param $current_user_id
     * @param $user_surveys
     */
    private function processMerge($new_template, $current_user_id, &$user_surveys){
        try {
            //process
            $user_surveys_ids = [];
            foreach ($user_surveys as $survey)
                $user_surveys_ids[] = $survey->ID;

            echo sprintf("found %s surveys id for member id %s ....", implode(", ", $user_surveys_ids), $current_user_id).PHP_EOL;

            $last_user_survey      = end($user_surveys);
            $new_survey_instance   = $this->buildSurvey($new_template->ID, $current_user_id, $last_user_survey->Lang);
            $former_entity_surveys = [];
            reset($user_surveys);

            // get former deployments ...
            foreach ($user_surveys as $former_survey) {
                foreach ($former_survey->getSteps() as $step) {
                    if (!($step instanceof ISurveyDynamicEntityStep)) continue;
                    foreach ($step->getEntitySurveys() as $former_entity_survey) {
                        $deployment_name = $former_entity_survey->getFriendlyName();
                        if (isset($former_entity_surveys[$deployment_name]) &&
                            // if former deployment is recent than current one, skip it
                            new DateTime($former_entity_surveys[$deployment_name]->LastEdited) > new DateTime($former_entity_survey->LastEdited))
                            continue;
                        echo sprintf("adding deployment id %s (%s) for member id %s", $former_entity_survey->ID, $deployment_name, $current_user_id) . PHP_EOL;
                        $former_entity_surveys[$deployment_name] = $former_entity_survey;
                    }
                }
            }
            // merge surveys
            echo sprintf("created new survey instance for member id %s - former survey id %s - new survey id %s", $current_user_id, $last_user_survey->ID, $new_survey_instance->ID).PHP_EOL;
            echo sprintf("translating answers values from former survey id %s to new survey id %s", $last_user_survey->ID, $new_survey_instance->ID).PHP_EOL;
            foreach ($new_survey_instance->getSteps() as $step) {
                if ($step instanceof ISurveyRegularStep) {
                    $former_step = $last_user_survey->getStep($step->template()->title());
                    if (is_null($former_step)) continue;

                    $former_answers = [];
                    foreach ($former_step->Answers() as $answer) {
                        $new_question = $step->Template()->Questions()->filter('Name', $answer->Question()->Name)->first();
                        if(is_null($new_question) || is_null($answer->Value)) continue;
                        $former_answers[$answer->Question()->Name] = SurveyAnswerValueTranslator::translate($answer->Value, $answer->Question(), $new_question);
                    }

                    echo sprintf("got %s answers from former step %s", count($former_answers), $former_step->template()->title()) . PHP_EOL;
                    $this->completeStep($step, $former_answers, $new_survey_instance->Lang);
                }
                if ($step instanceof ISurveyDynamicEntityStep && count($former_entity_surveys) > 0) {
                    echo sprintf("processing deployments for member id %s (%s)", $current_user_id, count($former_entity_surveys)) . PHP_EOL;
                    foreach ($former_entity_surveys as $key => $former_entity_survey) {
                        // create new deployment
                        echo sprintf("adding deployment %s for member id %s", $key, $current_user_id) . PHP_EOL;
                        $new_entity_survey = $this->buildEntitySurvey($step, $current_user_id, $new_survey_instance->Lang);
                        echo sprintf("created new deployment for member id %s - former deployment id %s - new deployment id %s", $current_user_id, $former_entity_survey->ID, $new_entity_survey->ID).PHP_EOL;
                        echo sprintf("translating answers values from former deployment id %s to new deployment id %s", $former_entity_survey->ID, $new_entity_survey->ID).PHP_EOL;
                        foreach ($new_entity_survey->getSteps() as $new_survey_instance_step) {
                            $former_step = $former_entity_survey->getStep($new_survey_instance_step->template()->title());
                            if (is_null($former_step)) continue;

                            $entity_survey_former_answers = [];
                            foreach ($former_step->Answers() as $answer) {
                                $new_question = $new_survey_instance_step->Template()->Questions()->filter('Name', $answer->Question()->Name)->first();
                                if(is_null($new_question) || is_null($answer->Value)) continue;
                                $entity_survey_former_answers[$answer->Question()->Name] = SurveyAnswerValueTranslator::translate($answer->Value, $answer->Question(), $new_question);
                            }

                            echo sprintf("got %s answers from former step %s", count($entity_survey_former_answers), $former_step->template()->title()) . PHP_EOL;
                            $this->completeStep($new_survey_instance_step, $entity_survey_former_answers, $new_survey_instance->Lang);
                        }

                        $new_entity_survey->Created    = $former_entity_survey->Created;
                        $new_entity_survey->LastEdited = $former_entity_survey->LastEdited;
                        $new_entity_survey->write();
                    }
                }
            }
            $new_survey_instance->Created    = $last_user_survey->Created;
            $new_survey_instance->LastEdited = $last_user_survey->LastEdited;
            $new_survey_instance->write();
        }
        catch (Exception $ex){
            echo $ex->getMessage().PHP_EOL;
        }
    }

}