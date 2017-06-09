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

interface ISurveyRegularStep extends ISurveyStep {
    /**
     * @return ISurveyAnswer[]
     */
    public function getAnswers();

    /**
     * @param ISurveyAnswer $new_answer
     * @return void
     */
    public function addAnswer(ISurveyAnswer $new_answer);

    /**
     * @return void
     */
    public function clearAnswers();

    /**
     * @param int $answer_template_id
     * @return ISurveyAnswer
     */
    public function getAnswerByTemplateId($answer_template_id);

    /**
     * @param string $name
     * @return ISurveyAnswer
     */
    public function getAnswerByName($name);

    /**
     * @return array
     */
    public function getCurrentAnswersSnapshotState();

}