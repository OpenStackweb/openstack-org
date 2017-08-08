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
 * Class SurveyTemplateManager
 */
final class SurveyTemplateManager implements ISurveyTemplateManager
{

    /**
     * @var ITransactionManager
     */
    private $tx_manager;

    /**
     * @var ISurveyTemplateRepository
     */
    private $repository;

    /**
     * SurveyTemplateManager constructor.
     * @param ISurveyTemplateRepository $repository
     * @param ITransactionManager $tx_manager
     */
    public function __construct(ISurveyTemplateRepository $repository, ITransactionManager $tx_manager)
    {
        $this->repository = $repository;
        $this->tx_manager = $tx_manager;
    }

    /**
     * @param int $template_id
     * @param bool $include_sub_templates
     * @return array
     * @throws NotFoundEntityException
     */
    public function getAllFreeTextQuestionByTemplate($template_id, $include_sub_templates = true)
    {
        return $this->tx_manager->transaction(function() use($template_id, $include_sub_templates){

            $template    = $this->repository->getById($template_id);
            if(is_null($template)) throw new NotFoundEntityException();

            $questions = [];
            foreach($template->getSteps() as $step)
            {
                if($step instanceof ISurveyRegularStepTemplate){
                    foreach($step->getQuestions() as $question){
                        if($question->Type() == 'TextArea' || $question->Type() == 'TextBox'){
                            $questions[] = $question;
                        }
                    }
                }
                else if ($step instanceof ISurveyDynamicEntityStepTemplate && $include_sub_templates){
                    $entity = $step->getEntity();
                    $questions = array_merge($questions, $this->getAllFreeTextQuestionByTemplate($entity->getIdentifier(), $include_sub_templates));
                }
            }

            return $questions;
        });
    }
}