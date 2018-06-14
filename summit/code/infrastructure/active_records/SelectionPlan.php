<?php

/**
 * Copyright 2018 Openstack Foundation
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
class SelectionPlan extends DataObject implements ISelectionPlan
{

    use TimeZoneEntity;

    /**
     * @return string
     */
    public function getTimeZoneIdentifier(){
        if($this->SummitID > 0)
            return $this->Summit()->TimeZoneIdentifier;
        return "";
    }

    static $db = array
    (
        'Name'                          => 'Varchar(255)',
        'Enabled'                       => 'Boolean(1)',
        'SubmissionBeginDate'           => 'SS_Datetime',
        'SubmissionEndDate'             => 'SS_Datetime',
        'VotingBeginDate'               => 'SS_Datetime',
        'VotingEndDate'                 => 'SS_Datetime',
        'SelectionBeginDate'            => 'SS_Datetime',
        'SelectionEndDate'              => 'SS_Datetime',
        'MaxSubmissionAllowedPerUser'   => 'Int'
    );

    static $has_one = array
    (
        'Summit'    => 'Summit',
    );

    static $many_many = array
    (
        'CategoryGroups'    => 'PresentationCategoryGroup',
    );

    private static $has_many = [
        'Presentations'                => 'Presentation',
    ];

    private static $summary_fields = array
    (
        'ID'        => 'ID',
        'Name'      => 'Name'
    );

    protected function validate()
    {
        $valid = parent::validate();
        if (!$valid->valid()) {
            return $valid;
        }

        if ($this->Enabled) {
            // we neeed to test against all current and future summits
            $utc_now = new DateTime('now', new DateTimeZone('UTC'));
            $summits = Summit::get()->where("( SummitBeginDate <= '".$utc_now->format('Y-m-d H:i:s'). "' AND SummitEndDate >= '".$utc_now->format('Y-m-d H:i:s')."') OR ( SummitBeginDate > '".$utc_now->format('Y-m-d H:i:s')."' )")->toArray();
            $summits = array_merge($summits, [$this->Summit()]);
            foreach($summits as $summit) {
                $other_selection_plans = $summit->getActiveSelectionPlans()->filter('ID:not', $this->ID);
                foreach ($other_selection_plans as $plan) {
                    if ($plan->SubmissionBeginDate < $this->SubmissionEndDate && $plan->SubmissionEndDate > $this->SubmissionBeginDate) {
                        return $valid->error('Submission Dates are in conflict with plan ' . $plan->Name .' from Summit '.$summit->Title);
                    }
                    if ($plan->VotingBeginDate < $this->VotingEndDate && $plan->VotingEndDate > $this->VotingBeginDate) {
                        return $valid->error('Voting Dates are in conflict with plan ' . $plan->Name.' from Summit '.$summit->Title);
                    }
                    if ($plan->SelectionBeginDate < $this->SelectionEndDate && $plan->SelectionEndDate > $this->SelectionBeginDate) {
                        return $valid->error('Selection Dates are in conflict with plan ' . $plan->Name.' from Summit '.$summit->Title);
                    }
                }
            }
        }

        return $valid;
    }

    public function getCMSFields()
    {
        $fields = FieldList::create(TabSet::create('Root'));

        $fields->addFieldToTab('Root.Main', new CheckboxField('Enabled', 'Enabled', 1));
        $fields->addFieldToTab('Root.Main', new TextField('Name'));

        $summit_time_zone = $this->Summit()->TimeZoneIdentifier;
        $fields->addFieldToTab('Root.Main', new HeaderField("All dates below are in <span style='color:red;'>$summit_time_zone</span> time."));

        $fields->addFieldToTab('Root.Main', $date = new DatetimeField('SubmissionBeginDate', "When do submissions begin?"));
        $date->getDateField()->setConfig('showcalendar', true);
        $date->getDateField()->setConfig('dateformat', 'dd/MM/yyyy');
        $fields->addFieldToTab('Root.Main', $date = new DatetimeField('SubmissionEndDate', "When do submissions end?"));
        $date->getDateField()->setConfig('showcalendar', true);
        $date->getDateField()->setConfig('dateformat', 'dd/MM/yyyy');
        $fields->addFieldToTab('Root.Main', $date = new DatetimeField('VotingBeginDate', "When does voting begin?"));
        $date->getDateField()->setConfig('showcalendar', true);
        $date->getDateField()->setConfig('dateformat', 'dd/MM/yyyy');
        $fields->addFieldToTab('Root.Main', $date = new DatetimeField('VotingEndDate', "When does voting end?"));
        $date->getDateField()->setConfig('showcalendar', true);
        $date->getDateField()->setConfig('dateformat', 'dd/MM/yyyy');
        $fields->addFieldToTab('Root.Main', $date = new DatetimeField('SelectionBeginDate', "When do selections begin?"));
        $date->getDateField()->setConfig('showcalendar', true);
        $date->getDateField()->setConfig('dateformat', 'dd/MM/yyyy');
        $fields->addFieldToTab('Root.Main', $date = new DatetimeField('SelectionEndDate', "When do selections end?"));
        $date->getDateField()->setConfig('showcalendar', true);
        $date->getDateField()->setConfig('dateformat', 'dd/MM/yyyy');
        $fields->addFieldToTab('Root.Main', new HiddenField('SummitID'));

        $fields->addFieldToTab('Root.Main', new NumericField('MaxSubmissionAllowedPerUser', 'Max. Submissions Per User'));

        if($this->ID > 0)
        {
            $config = GridFieldConfig_RelationEditor::create(10);
            $config->removeComponentsByType('GridFieldAddNewButton');
            $completer = $config->getComponentByType('GridFieldAddExistingAutocompleter');
            $completer->setSearchList(PresentationCategoryGroup::get()->filter('SummitID', $this->Summit()->ID));
            $categories = new GridField('CategoryGroups', 'Category Groups', $this->CategoryGroups(), $config);
            $fields->addFieldToTab('Root.Main', $categories);
        }

        return $fields;
    }

    public function getIdentifier()
    {
        return (int)$this->getField('ID');
    }

    public function setSubmissionBeginDate($value)
    {
        $this->setDateTimeFromLocalToUTC($value, 'SubmissionBeginDate');
    }

    public function getSubmissionBeginDate($format = "Y-m-d H:i:s")
    {
        return $this->getFromUTCtoLocal('SubmissionBeginDate', $format);
    }

    public function setSubmissionEndDate($value)
    {
        $this->setDateTimeFromLocalToUTC($value, 'SubmissionEndDate');
    }

    public function getSubmissionEndDate($format = "Y-m-d H:i:s")
    {
        return $this->getFromUTCtoLocal('SubmissionEndDate', $format);
    }

    public function setVotingBeginDate($value)
    {
        $this->setDateTimeFromLocalToUTC($value, 'VotingBeginDate');
    }

    public function getVotingBeginDate($format = "Y-m-d H:i:s")
    {
        return $this->getFromUTCtoLocal('VotingBeginDate', $format);
    }

    public function setVotingEndDate($value)
    {
        $this->setDateTimeFromLocalToUTC($value, 'VotingEndDate');
    }

    public function getVotingEndDate($format = "Y-m-d H:i:s")
    {
        return $this->getFromUTCtoLocal('VotingEndDate', $format);
    }

    public function setSelectionBeginDate($value)
    {
        $this->setDateTimeFromLocalToUTC($value, 'SelectionBeginDate');
    }

    public function getSelectionBeginDate($format = "Y-m-d H:i:s")
    {
        return $this->getFromUTCtoLocal('SelectionBeginDate', $format);
    }

    public function setSelectionEndDate($value)
    {
        $this->setDateTimeFromLocalToUTC($value, 'SelectionEndDate');
    }

    public function getSelectionEndDate($format = "Y-m-d H:i:s")
    {
        return $this->getFromUTCtoLocal('SelectionEndDate', $format);
    }

    public function getPublicCategoryGroups()
    {
        return $this->CategoryGroups()->filter('ClassName', 'PresentationCategoryGroup')->toArray();
    }

    public function getPrivateCategoryGroups()
    {
        return $this->CategoryGroups()->filter('ClassName', 'PrivatePresentationCategoryGroup')->toArray();
    }

    public function getCategories()
    {
        $categories = [];

        foreach ($this->CategoryGroups() as $group) {
            $categories = array_merge($categories, $group->Categories()->toArray());
        }

        return $categories;
    }

    public function getVotingCategories()
    {
        $categories = $this->getCategories();
        //die('c:'.count($categories));
        $voting_categories = array_filter($categories, function($obj){
            return $obj->VotingVisible;
        });

        return $voting_categories;
    }

    public function getSelectionCategories()
    {
        $categories = $this->getCategories();

        $chair_categories = array_filter($categories, function($obj){
            return $obj->ChairVisible;
        });

        return $chair_categories;
    }

    public function getStageStatus($stage) {

        $start_date = $this->getField("{$stage}BeginDate");
        $end_date = $this->getField("{$stage}EndDate");

        if (empty($start_date) || empty($end_date)) {
            return null;
        }

        $start_date = new DateTime($start_date, new DateTimeZone('UTC'));
        $end_date = new DateTime($end_date, new DateTimeZone('UTC'));
        $now = new \DateTime('now', new DateTimeZone('UTC'));

        if ($now > $end_date) {
            return Summit::STAGE_FINISHED;
        } else if ($now < $start_date) {
            return Summit::STAGE_UNSTARTED;
        } else {
            return Summit::STAGE_OPEN;
        }
    }

    public function isVotingOpen()
    {
        return $this->getStageStatus('Voting') === Summit::STAGE_OPEN;
    }

    public function isCallForPresentationsOpen()
    {
        return $this->getStageStatus('Submission') === Summit::STAGE_OPEN;
    }

    public function isSelectionOpen()
    {
        return $this->getStageStatus('Selection') === Summit::STAGE_OPEN;
    }

    public function getMaxSubmissions() {
        $max_submissions = $this->MaxSubmissionAllowedPerUser;
        if (!$max_submissions && $this->SummitID) {
            $max_submissions = $this->Summit()->MaxSubmissionAllowedPerUser;
        }

        return $max_submissions;
    }
}