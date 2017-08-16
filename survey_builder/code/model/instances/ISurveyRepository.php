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

interface ISurveyRepository extends IEntityRepository {

    /**
     * @param int $template_id
     * @param int $creator_id
     * @return ISurvey|null
     */
    public function getByTemplateAndCreator($template_id, $creator_id);

    /**
     * @param int $template_id
     * @param int $question_id
     * @param array $values
     * @param PagingInfo $page_info
     * @param Order|null $order
     * @param string $survey_lang
     * @return array
     */
    public function getByTemplateAndAnswerValue($template_id, $question_id , array $values = [], PagingInfo $page_info, Order $order = null, $survey_lang = 'ALL');
}