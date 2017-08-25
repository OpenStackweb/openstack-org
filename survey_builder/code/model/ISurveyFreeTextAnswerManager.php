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
interface ISurveyFreeTextAnswerManager
{
    /**
     * @param int $question_id
     * @param int $page
     * @param int $page_size
     * @param string $search_term
     * @param string $languages
     * @return array
     * @throws NotFoundEntityException
     */
    public function getFreeTextAnswerByQuestion($question_id, $page, $page_size, $search_term, $languages);

    /**
     * @param int $question_id
     * @return array
     */
    public function getAllFreeTextTagsByQuestion($question_id);

    /**
     * @param int $template_id
     * @param int $question_id
     * @param int $answer_id
     * @param array $data
     * @throws NotFoundEntityException
     * @return void
     */
    public function updateFreeTextAnswer($template_id, $question_id, $answer_id, array $data);

    /**
     * @param int $template_id
     * @param int $question_id
     * @param int $answer_id
     * @param string $tag
     * @throws NotFoundEntityException
     * @return void
     */
    public function addTagToFreeTextAnswers($template_id, $question_id, $answer_id, $tag);

    /**
     * @param int $template_id
     * @param int $question_id
     * @param int $answer_id
     * @param string $tag
     * @throws NotFoundEntityException
     * @return void
     */
    public function deleteTagToFreeTextAnswers($template_id, $question_id, $answer_id, $tag);

    /**
     * @param int $template_id
     * @param int $question_id
     * @param array $tags
     * @param string $replace_tag
     * @throws NotFoundEntityException
     * @return void
     */
    public function mergeTagsInFreeTextQuestion($template_id, $question_id, $tags, $replace_tag);

    /**
     * @param int $question_id
     * @return array
     * @throws NotFoundEntityException
     */
    public function getLanguagesByQuestion($question_id);
}