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
    public function getSurveyByTemplateAndCreator($template_id, $creator_id){

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
     * @param ISurveyStep $current_step
     * @return ISurveyStep
     */
    public function saveCurrentStep(ISurveyStep $current_step, array $data)
    {
        // TODO: Implement saveCurrentStep() method.
    }
}