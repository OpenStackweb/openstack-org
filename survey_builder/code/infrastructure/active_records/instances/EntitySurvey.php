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

    static $db = array
    ();

    static $indexes = array
    ();

    static $has_one = array
    (
        'Template' => 'EntitySurveyTemplate',
        'Parent'   => 'Survey',
        'Owner'    => 'SurveyDynamicEntityStep',
        'EditedBy' => 'Member'
    );

    static $many_many = array
    (
        'EditorTeam' => 'Member'
    );

    static $has_many = array
    ();

    private static $defaults = array
    ();

    /**
     * @return ISurvey
     */
    public function parent()
    {
        return AssociationFactory::getInstance()->getMany2OneAssociation($this, 'Parent')->getTarget();
    }

    /**
     * @return ISurveyDynamicEntityStep
     */
    public function owner()
    {
        return AssociationFactory::getInstance()->getMany2OneAssociation($this, 'Owner')->getTarget();
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
     * @return bool
     */
    public function isTeamEditionAllowed()
    {
        return $this->Template()->UseTeamEdition;
    }

    /**
     * @param ICommunityMember $member
     * @return void
     */
    public function addTeamMember(ICommunityMember $member)
    {
        AssociationFactory::getInstance()->getMany2ManyAssociation($this, 'EditorTeam')->add($member);
    }

    /**
     * @param ICommunityMember $member
     * @return void
     */
    public function removeTeamMember(ICommunityMember $member)
    {
        AssociationFactory::getInstance()->getMany2ManyAssociation($this, 'EditorTeam')->remove($member);
    }

    /**
     * @return ICommunityMember
     */
    public function getUpdateBy()
    {
        return AssociationFactory::getInstance()->getMany2OneAssociation($this, 'EditedBy')->getTarget();
    }

    /**
     * @return ICommunityMember[]
     */
    public function getTeamMembers()
    {
        return AssociationFactory::getInstance()->getMany2ManyAssociation($this, 'EditorTeam')->toArray();
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
     * @param ICommunityMember $member
     * @return bool
     */
    public function isTeamMember(ICommunityMember $member)
    {
        $member = $this->EditorTeam()->filter('MemberID', $member->getIdentifier())->first();

        return !is_null($member);
    }

}