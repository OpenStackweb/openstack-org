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
use ICanBoogie\Inflector;
/**
 * Class DynamicStepForm
 */
class DynamicStepForm extends AbstractStepForm {

    /**
     * @var ISurveyDynamicEntityStep
     */
    private $step;

    /**
     * @param Controller $controller
     * @param String $name
     * @param FieldList $fields
     * @param FieldList $actions
     * @param ISurveyDynamicEntityStep $step
     * @param null $validator
     */
    function __construct
    (
        $controller,
        $name,
        FieldList $fields,
        FieldList $actions,
        ISurveyDynamicEntityStep $step,
        $validator = null
    )
    {
        parent::__construct($controller, $name, $fields, $actions, $validator);
        $this->step = $step;
    }

    /**
     * @return ISurveyDynamicEntityStep
     */
    public function CurrentStep()
    {
        return $this->step;
    }

    public function Controller()
    {
        return Controller::curr();
    }

    public function EntitiesSurveys()
    {
        $own_entity_surveys  = $this->step->getEntitySurveys();
        $current_member      = Member::currentUser();
        $step_template       = $this->step->template();
        $team_entity_surveys = $current_member->TeamEntitySurveys()->filter('TemplateID',$step_template->getEntity()->getIdentifier())->toArray();
        return new ArrayList(array_merge($own_entity_surveys, $team_entity_surveys));
    }

    /**
     * @return string
     */
    public function EntityIconUrl()
    {
        $icon     = $this->step->template()->EntityIcon();
        $icon_url = '/themes/openstack/images/user-survey/cloud.png';
        if($icon->ID > 0){
            $icon_url = $icon->Link();
        }
        return $icon_url;
    }

    public function EntityFriendlyName($id)
    {
        $entity = $this->step->getEntitySurvey(intval($id));
        if(is_null($entity))
        {
            $current_member      = Member::currentUser();
            $entity              = $current_member->TeamEntitySurveys()->filter('EntitySurveyID',intval($id))->first();
        }
        return !is_null($entity)? $entity->getFriendlyName(): $id;
    }

    /**
     * @return bool
     */
    public function CanSkipStep(){
        return $this->step->canSkip();
    }

    /**
     * @return String
     */
    public function SkipStepUrl(){
        return Controller::join_links(
            Controller::curr()->Link(),
            $this->step->template()->title(),
            'skip-step'
        );
    }

    /**
     * @return string
     */
    public function EntityNameLowerCase(){
        return strtolower($this->step->template()->getEntity()->getEntityName());
    }

    /**
     * @return string
     */
    public function EntityNameLowerCasePlural(){
        $inflector = Inflector::get('en');
        $word      = $this->EntityNameLowerCase();
        return $inflector->pluralize($word);
    }

}