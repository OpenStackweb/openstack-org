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
final class SurveyFreeTextAnswerManager implements ISurveyFreeTextAnswerManager
{

    /**
     * @var ISurveyAnswerRepository
     */
    private $repository;

    /**
     * @var ITransactionManager
     */
    private $tx_manager;

    /**
     * SurveyFreeTextAnswerManager constructor.
     * @param ISurveyAnswerRepository $repository
     * @param ITransactionManager $tx_manager
     */
    public function __construct(ISurveyAnswerRepository $repository, ITransactionManager $tx_manager)
    {
        $this->repository  = $repository;
        $this->tx_manager = $tx_manager;
    }


    /**
     * @param int $question_id
     * @param int $page
     * @param int $page_size
     * @param string $search_term
     * @param string $languages
     * @return array
     * @throws NotFoundEntityException
     */
    public function getFreeTextAnswerByQuestion($question_id, $page, $page_size, $search_term, $languages)
    {
       return $this->tx_manager->transaction(function() use($question_id, $page, $page_size, $search_term, $languages){
          return $this->repository->getPaginatedFreeTextAnswers($question_id, $page, $page_size, $search_term, $languages);
       });
    }

    /**
     * @param int $template_id
     * @param int $question_id
     * @param int $answer_id
     * @param array $data
     * @throws NotFoundEntityException
     * @return mixed
     */
    public function updateFreeTextAnswer($template_id, $question_id, $answer_id, array $data)
    {

        $this->tx_manager->transaction(function() use($template_id, $question_id, $answer_id, $data){
            $answer = $this->repository->getById($answer_id);
            if(is_null($answer)) throw new NotFoundEntityException('SurveyAnswer');

            if($answer->question()->getIdentifier() != $question_id)
                throw new NotFoundEntityException('SurveyQuestionTemplate');

            $survey = $answer->question()->step()->survey();
            $survey_id = (is_a($survey, 'EntitySurveyTemplate')) ? $survey->Parent()->getIdentifier() : $survey->getIdentifier();

            if($survey_id != $template_id)
                throw new NotFoundEntityException('SurveyTemplate');

            $answer->Value       = trim($data['value']);
            $answer->UpdatedByID = Member::currentUserID();
        });

    }

    /**
     * @param int $template_id
     * @param int $question_id
     * @param int $answer_id
     * @param string $tag
     * @throws NotFoundEntityException
     * @throws EntityValidationException
     * @return void
     */
    public function addTagToFreeTextAnswers($template_id, $question_id, $answer_id, $tag)
    {
        $this->tx_manager->transaction(function() use($template_id, $question_id, $answer_id, $tag){

            $answer = $this->repository->getById($answer_id);
            if(is_null($answer)) throw new NotFoundEntityException('SurveyAnswer');

            if($answer->question()->getIdentifier() != $question_id)
                throw new NotFoundEntityException('SurveyQuestionTemplate');

            $survey = $answer->question()->step()->survey();
            $survey_id = (is_a($survey, 'EntitySurveyTemplate')) ? $survey->Parent()->getIdentifier() : $survey->getIdentifier();

            if($survey_id != $template_id)
                throw new NotFoundEntityException('SurveyTemplate');

            $tag_value = SurveyAnswerTag::trimTag($tag);

            $tag = SurveyAnswerTag::get()->filter('Value', $tag_value)->first();

            if(is_null($tag)){
                $tag = new SurveyAnswerTag();
                $tag->Value = $tag_value;
                $tag->CreatedByID = Member::currentUserID();
                $tag->Type = SurveyAnswerTag::TypeCustom;
                $tag->write();
            }

            $answer->Tags()->add($tag);
        });
    }

    /**
     * @param int $template_id
     * @param int $question_id
     * @param int $answer_id
     * @param string $tag
     * @throws NotFoundEntityException
     * @throws EntityValidationException
     * @return void
     */
    public function deleteTagToFreeTextAnswers($template_id, $question_id, $answer_id, $tag)
    {
        $this->tx_manager->transaction(function() use($template_id, $question_id, $answer_id, $tag){

            $answer = $this->repository->getById($answer_id);
            if(is_null($answer)) throw new NotFoundEntityException('SurveyAnswer');

            if($answer->question()->getIdentifier() != $question_id)
                throw new NotFoundEntityException('SurveyQuestionTemplate');

            $survey = $answer->question()->step()->survey();
            $survey_id = (is_a($survey, 'EntitySurveyTemplate')) ? $survey->Parent()->getIdentifier() : $survey->getIdentifier();

            if($survey_id != $template_id)
                throw new NotFoundEntityException('SurveyTemplate');

            $tag_value = SurveyAnswerTag::trimTag($tag);

            $tag = $answer->Tags()->filter('Value', $tag_value)->first();
            if(is_null($tag))
                throw new EntityValidationException('tag does not belong to answer');

            $answer->Tags()->remove($tag);
        });
    }

    /**
     * @param int $question_id
     * @return array
     */
    public function getAllFreeTextTagsByQuestion($question_id)
    {
        return $this->repository->getAllFreeTextAnswerTagsByQuestion($question_id);
    }

    /**
     * @param int $template_id
     * @param int $question_id
     * @param array $tags
     * @param string $replace_tag
     * @throws NotFoundEntityException
     * @return void
     */
    public function mergeTagsInFreeTextQuestion($template_id, $question_id, $tags_to_replace, $replace_tag)
    {
        $this->tx_manager->transaction(function() use($template_id, $question_id, $tags_to_replace, $replace_tag){

            $question = SurveyQuestionTemplate::get()->byID($question_id);
            if(is_null($question)) throw new NotFoundEntityException('SurveyQuestionTemplate');

            $survey = $question->step()->survey();
            $survey_id = (is_a($survey, 'EntitySurveyTemplate')) ? $survey->Parent()->getIdentifier() : $survey->getIdentifier();

            if($survey_id != $template_id)
                throw new NotFoundEntityException('SurveyTemplate');

            $replace_tag_value = SurveyAnswerTag::trimTag($replace_tag);

            $replace_tag_obj = SurveyAnswerTag::get()->filter('Value', $replace_tag)->first();

            if(is_null($replace_tag_obj)){
                $replace_tag_obj = new SurveyAnswerTag();
                $replace_tag_obj->Value = $replace_tag_value;
                $replace_tag_obj->CreatedByID = Member::currentUserID();
                $replace_tag_obj->Type = SurveyAnswerTag::TypeCustom;
                $replace_tag_obj->write();
            }

            $answers = $question->Answers()->filter(array('Tags.ID' => $tags_to_replace));
            foreach($answers as $answer) {
                $answer->Tags()->filter('ID',$tags_to_replace)->removeAll();
                if ($answer->Tags()->filter('Value',$replace_tag_value)->Count() == 0){
                    $answer->Tags()->add($replace_tag_obj);
                }
            }
        });
    }

    /**
     * @param int $question_id
     * @return array
     * @throws NotFoundEntityException
     */
    public function getLanguagesByQuestion($question_id)
    {
        return $this->tx_manager->transaction(function() use($question_id){
            return $this->repository->getLanguagesByQuestion($question_id);
        });
    }
}