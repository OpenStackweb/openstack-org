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

interface ISurveyAnswerRepository extends IEntityRepository {

    /**
     * @param int $question_id
     * @param array filters
     * @return ArrayList
     */
    public function getByQuestionAndFilters($question_id, $filters);

    /**
     * @param int $question_id
     * @param ArrayList $answers
     * @return ArrayList
     */
    public function mapAnswers($question_id, $answers);

    /**
     * @param int $question_id
     * @param int $page
     * @param int $page_size
     * @param string $search_term
     * @param string $languages
     * @return array
     * @throws NotFoundEntityException
     */
    public function getPaginatedFreeTextAnswers($question_id, $page, $page_size, $search_term, $languages);

    /**
     * @param int $question_id
     * @return array
     */
    public function getAllFreeTextAnswerTagsByQuestion($question_id);

    /**
     * @param int $question_id
     * @return array
     */
    public function getCountForTags($question_id);

    /**
     * @param int $question_id
     * @return array
     */
    public function getLanguagesByQuestion($question_id);
}