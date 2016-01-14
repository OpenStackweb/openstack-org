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
interface ISurveyTemplateFactory
{
    /**
     * @param $title
     * @param null $start_date
     * @param null $end_date
     * @param bool|false $enabled
     * @return ISurveyTemplate
     */
    public function  build($title, $start_date = null, $end_date = null, $enabled = false);

    /**
     * @param ISurveyStep $original_step
     * @return ISurveyStep
     */
    public function  cloneStep(ISurveyStep $original_step);

    /**
     * @param ISurveyTemplate $original_template
     * @param int|null $parent_id
     * @return ISurveyTemplate
     */
    public function cloneTemplate(ISurveyTemplate $original_template, $parent_id = null);


    /**
     * @param ISurveyQuestionTemplate $question
     * @return ISurveyQuestionTemplate
     */
    public function cloneQuestion(ISurveyQuestionTemplate $question);

    /**
     * @param IQuestionValueTemplate $value
     * @return IQuestionValueTemplate
     */
    public function cloneQuestionValue(IQuestionValueTemplate $value);
}