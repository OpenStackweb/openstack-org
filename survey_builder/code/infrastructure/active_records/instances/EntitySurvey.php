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
 * Class EntitySurvey
 */
class EntitySurvey extends Survey implements IEntitySurvey
{

    static $db = [];

    static $indexes = [];

    static $has_one = [
        'Template' => 'EntitySurveyTemplate',
        'Parent'   => 'Survey',
        'Owner'    => 'SurveyDynamicEntityStep',
        'EditedBy' => 'Member'
    ];

    static $many_many = [
        'EditorTeam' => 'Member'
    ];

    static $many_many_extraFields = [
        'EditorTeam' => ['EntitySurveyTeamMemberMailed' => 'Boolean']
    ];

    static $has_many = [];

    static $defaults = [];


    /**
     * @param Member $member
     * @return bool
     */
    public function hasTeamPermissions(Member $member){
       if($this->iAmOwner() || $this->isTeamMember($member)) return true;
       return false;
    }

    /**
     * @return ISurvey
     */
    public function parent()
    {
        return $this->getComponent('Parent');
    }

    /**
     * @return ISurveyDynamicEntityStep
     */
    public function owner()
    {
        return $this->getComponent('Owner');
    }

    protected function onBeforeDelete()
    {
        parent::onBeforeDelete();
    }

    /**
     * @return string
     */
    public function getFriendlyName()
    {
        $steps = $this->getSteps();
        foreach ($steps as $step) {
            if ($step instanceof ISurveyRegularStep) {
                foreach ($step->getAnswers() as $a) {
                    if ($a->question()->Type() === 'TextBox' && !empty($a->value())) {
                        return $a->value();
                    }
                }
            }
        }
        return $this->ID;
    }

    /**
     * @return string|null
     */
    public function getOrganization(){
        // search first on entity
        foreach ($this->getSteps() as $step) {
            if ($step instanceof ISurveyRegularStep && $step->template()->getQuestionByClassName('SurveyOrganizationQuestionTemplate')) {
                foreach ($step->getAnswers() as $a) {
                    if ($a->question()->Type() === SurveyOrganizationQuestionTemplate::FieldName && !empty($a->value())) {
                        return $a->value();
                    }
                }
            }
        }
        // check parent survey...
        foreach ($this->Parent()->getSteps() as $step) {
            if ($step instanceof ISurveyRegularStep && $step->template()->getQuestionByClassName('SurveyOrganizationQuestionTemplate')) {
                foreach ($step->getAnswers() as $a) {
                    if ($a->question()->Type() === SurveyOrganizationQuestionTemplate::FieldName && !empty($a->value())) {
                        return $a->value();
                    }
                }
            }
        }
        return null;
    }

     /**
     * @return bool
     */
    public function isTeamEditionAllowed()
    {
        return $this->Template()->UseTeamEdition;
    }

    /**
     * @param Member $member
     * @return void
     */
    public function addTeamMember(Member $member, $extraFields = null)
    {
        $this->EditorTeam()->add($member, $extraFields);
    }

    /**
     * @param Member $member
     * @return void
     */
    public function removeTeamMember(Member $member)
    {
        $this->EditorTeam()->remove($member);
    }

    /**
     * @return Member
     */
    public function getUpdateBy()
    {
        return $this->getComponent('EditedBy');
    }

    /**
     * @return Member[]
     */
    public function getTeamMembers()
    {
        return $this->EditorTeam()->toArray();
    }

    protected function onBeforeWrite()
    {
        parent::onBeforeWrite();
        $this->EditedByID = Member::currentUserID();
    }

    /**
     * @return bool
     */
    public function iAmOwner()
    {
        return intval($this->CreatedByID) === intval(Member::currentUserID());
    }

    /**
     * @param Member $member
     * @return bool
     */
    public function isTeamMember(Member $member)
    {
        $member = $this->EditorTeam()->filter('MemberID', $member->getIdentifier())->first();

        return !is_null($member);
    }

}