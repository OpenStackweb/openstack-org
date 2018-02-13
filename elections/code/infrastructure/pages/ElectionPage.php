<?php
/**
 * Copyright 2014 Openstack Foundation
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
final class ElectionPage extends Page {

    private static $db = [
        'CandidateApplicationFormBioLabel'                     => 'Text',
        'CandidateApplicationFormRelationshipToOpenStackLabel' => 'Text',
        'CandidateApplicationFormExperienceLabel'              => 'Text',
        'CandidateApplicationFormBoardsRoleLabel'              => 'Text',
        'CandidateApplicationFormTopPriorityLabel'             => 'Text',
    ];

    static $has_one = [
        'CurrentElection' => 'Election'
    ];

    public function getCandidateApplicationFormBioLabel(){
        $res = $this->getField("CandidateApplicationFormBioLabel");
        if(empty($res)) $res = "Provide Brief Biography of Yourself";
        return $res;
    }

    public function getCandidateApplicationFormRelationshipToOpenStackLabel(){
        $res = $this->getField("CandidateApplicationFormRelationshipToOpenStackLabel");
        if(empty($res)) $res = "What is your relationship to OpenStack, and why is its success important to you? What would you say is your biggest contribution to OpenStack's success to date?";
        return $res;
    }

    public function getCandidateApplicationFormExperienceLabel(){
        $res = $this->getField("CandidateApplicationFormExperienceLabel");
        if(empty($res)) $res = "Describe your experience with other non profits or serving as a board member. How does your experience prepare you for the role of a board member?";
        return $res;
    }

    public function getCandidateApplicationFormBoardsRoleLabel(){
        $res = $this->getField("CandidateApplicationFormBoardsRoleLabel");
        if(empty($res)) $res = "What do you see as the Board's role in OpenStack's success?";
        return $res;
    }

    public function getCandidateApplicationFormTopPriorityLabel(){
        $res = $this->getField("CandidateApplicationFormTopPriorityLabel");
        if(empty($res)) $res = "What do you think the top priority of the Board should be over the next year?";
        return $res;
    }

    function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $drop   = new DropdownField('CurrentElectionID', 'Select An Election', Election::get()->map('ID','Name'));
        $drop->setEmptyString('No Election');
        $fields->addFieldToTab('Root.CurrentElection', $drop);
        // CandidateApplicationForm Labels
        $fields->addFieldToTab('Root.CandidateApplicationForm', new TextareaField("CandidateApplicationFormBioLabel","Bio Label"));
        $fields->addFieldToTab('Root.CandidateApplicationForm', new TextareaField("CandidateApplicationFormRelationshipToOpenStackLabel","Relationship To OpenStack Label"));
        $fields->addFieldToTab('Root.CandidateApplicationForm', new TextareaField("CandidateApplicationFormExperienceLabel","Experience Label"));
        $fields->addFieldToTab('Root.CandidateApplicationForm', new TextareaField("CandidateApplicationFormBoardsRoleLabel","Boards Role Label"));
        $fields->addFieldToTab('Root.CandidateApplicationForm', new TextareaField("CandidateApplicationFormTopPriorityLabel","Top Priority Label"));
        return $fields;
    }

    /**
     * @return bool|ElectionPage
     */
    public static function getCurrent(){
        $current_election = Election::getCurrent();
        if(is_null($current_election)) return false;
        return ElectionPage::get()->filter("CurrentElectionID", $current_election->ID)->first();
    }

    public static $validation_enabled = true;

    function validate()
    {
        if (!self::$validation_enabled) return ValidationResult::create();

        $valid = parent::validate();
        if (!$valid->valid()) {
            return $valid;
        }

        if($this->CurrentElectionID == 0){
            return $valid->error('You must set a current election!');
        }

        $res = ElectionPage::get()->filter([
            'ID:ExactMatch:not' => $this->ID,
            'CurrentElectionID' => $this->CurrentElectionID
        ])->count();

        if($res > 0){
            return $valid->error('Current election is already assigned to another ElectionPage!');
        }

        return $valid;
    }

}

class ElectionPage_Controller extends Page_Controller {

    function init() {
        parent::init();
    }

    static $allowed_actions = [
        'CandidateList',
        'CandidateListGold',
    ];

}
