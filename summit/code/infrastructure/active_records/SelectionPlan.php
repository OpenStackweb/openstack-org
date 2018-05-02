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

    static $db = array
    (
        'Name'                  => 'Varchar(255)',
        'Enabled'               => 'Boolean(1)',
        'SubmissionBeginDate'   => 'SS_Datetime',
        'SubmissionEndDate'     => 'SS_Datetime',
        'VotingBeginDate'       => 'SS_Datetime',
        'VotingEndDate'         => 'SS_Datetime',
        'SelectionBeginDate'    => 'SS_Datetime',
        'SelectionEndDate'      => 'SS_Datetime',
    );

    static $has_one = array
    (
        'Summit'    => 'Summit',
    );

    static $has_many = array
    (
        'CategoryGroups'    => 'PresentationCategoryGroup',
    );

    private static $summary_fields = array
    (
        'ID'        => 'ID',
        'Name'      => 'Name'
    );

    const STAGE_UNSTARTED = -1;
    const STAGE_OPEN = 0;
    const STAGE_FINISHED = 1;

    /**
     * @return int
     */
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
            return STAGE_FINISHED;
        } else if ($now < $start_date) {
            return STAGE_UNSTARTED;
        } else {
            return STAGE_OPEN;
        }
    }

    public function isVotingOpen()
    {
        return $this->getStageStatus('Voting') == STAGE_OPEN;
    }

    public function isCallForPresentationsOpen()
    {
        return $this->getStageStatus('Submission') == STAGE_OPEN;
    }

    public function isSelectionOpen()
    {
        return $this->getStageStatus('Selection') == STAGE_OPEN;
    }
}