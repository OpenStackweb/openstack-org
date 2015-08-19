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
class EntitySurveyEditorTeamField extends FormField
{

    /**
     * @var IEntitySurvey
     */
    private $entity_survey;

    /**
     * @param string $name
     * @param null $title
     * @param IEntitySurvey $entity_survey
     */
    public function __construct($name, $title = null, IEntitySurvey $entity_survey){
        parent::__construct($name, $title);
        $this->entity_survey = $entity_survey;
    }

    public function Field($properties = array())
    {
        Requirements::javascript('survey_builder/js/entity.survey.editor.team.field.js');
        Requirements::css('survey_builder/css/EntitySurveyEditorTeamField.css');

        return $this
            ->customise($properties)
            ->renderWith(array("EntitySurveyEditorTeamField"));
    }

    public function getTeamMembers()
    {
        return new ArrayList($this->entity_survey->getTeamMembers());
    }

}