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
class SurveyManager implements ISurveyManager {

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
     * @param ISurveyRepository $survey_repository
     * @param IEntityRepository $template_repository
     * @param IFoundationMemberRepository $member_repository
     * @param ISurveyBuilder $survey_builder
     * @param ITransactionManager $tx_manager
     */
    public function __construct(ISurveyRepository $survey_repository,
                                IEntityRepository $template_repository,
                                IFoundationMemberRepository $member_repository,
                                ISurveyBuilder $survey_builder,
                                ITransactionManager $tx_manager){

        $this->survey_repository   = $survey_repository;
        $this->template_repository = $template_repository;
        $this->member_repository   = $member_repository;
        $this->survey_builder      = $survey_builder;
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
     * @return ISurveyTemplate
     */
    public function getCurrentSurveyTemplate()
    {
        $query = New QueryObject();
        $now   = new \DateTime('now', new DateTimeZone('UTC'));
        $query->addAndCondition(QueryCriteria::lowerOrEqual('StartDate', $now->format('Y-m-d H:i:s')));
        $query->addAndCondition(QueryCriteria::greaterOrEqual('EndDate', $now->format('Y-m-d H:i:s')));
        $query->addAndCondition(QueryCriteria::equal('Enabled', 1));
        return $this->template_repository->getBy($query);
    }

    /**
     * @param array $data
     * @param ISurveyRegularStep $current_step
     * @return ISurveyStep
     */
    public function completeStep(ISurveyRegularStep $current_step, array $data)
    {

        $template_repository = $this->template_repository;
        $survey_repository   = $this->survey_repository;
        $survey_builder      = $this->survey_builder;
        $member_repository   = $this->member_repository;

        return $this->tx_manager->transaction(function() use($current_step, $data, $survey_builder, $member_repository, $template_repository, $survey_repository){

            $current_survey = $current_step->survey();

            $current_step->clearAnswers();
            foreach($current_step->template()->getQuestions() as $q){
                if(isset($data[$q->name()])){
                    // its has an answer set
                    $current_step->addAnswer( $survey_builder->buildAnswer($q, $data[$q->name()]) );
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
}