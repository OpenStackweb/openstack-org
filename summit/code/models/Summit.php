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
class Summit extends DataObject
{

    private static $db = array (
        'Name'                        => 'Varchar(255)',
        'Location'                    => 'Varchar(255)',
        'Title'                       => 'Varchar',
        'SummitBeginDate'             => 'SS_Datetime',
        'SummitEndDate'               => 'SS_Datetime',
        'SubmissionBeginDate'         => 'SS_Datetime',
        'SubmissionEndDate'           => 'SS_Datetime',
        'VotingBeginDate'             => 'SS_Datetime',
        'VotingEndDate'               => 'SS_Datetime',
        'SelectionBeginDate'          => 'SS_Datetime',
        'SelectionEndDate'            => 'SS_Datetime',
        'DateLabel'                   => 'Varchar',
        'Link'                        => 'Varchar',
        'RegistrationLink'            => 'Text',
        'Active'                      => 'Boolean',
        'ComingSoonBtnText'           => 'Text',
    );

    private static $has_many = array (
        'Presentations' => 'Presentation',
        'Categories' => 'PresentationCategory',
    );

    private static $summary_fields = array (
        'Title' => 'Title',
        'Status' => 'Status',
    );

    private static $searchable_fields = array (
    );

    public static function get_active()
    {
        $summit = Summit::get()->filter(array(
            'Active' => true
        ))->first();

        return $summit ?: Summit::create();
    }

    public function checkRange($key)
    {
        $beginField = "{$key}BeginDate";
        $endField   = "{$key}EndDate";

        if (!$this->hasField($beginField) || !$this->hasField($endField)) return false;

        return (time() > $this->obj($beginField)->format('U')) && (time() < $this->obj($endField)->format('U'));
    }


    public function getStatus()
    {
        if (!$this->Active) return "INACTIVE";

        if ($this->checkRange("Submission")) return "ACCEPTING SUBMISSIONS";
        if ($this->checkRange("Voting")) return "COMMUNITY VOTING";
        if ($this->checkRange("Selection")) return "TRACK CHAIR SELECTION";
        if ($this->checkRange("Summit")) return "SUMMIT IS ON";

        return "DRAFT";
    }


    public function getTitle(){
        $title = $this->getField('Title');
        $name = $this->getField('Name');
        return empty($title)? $name : $title;
    }

    function TalksByMemberID($memberID)
    {

        $SpeakerList = new ArrayList();

        // Pull any talks that belong to this Summit and are owned by member
        $talksMemberOwns = $this->Talks("`OwnerID` = " . $memberID . " AND `SummitID` = " . $this->ID);
        $SpeakerList->merge($talksMemberOwns);

        // Now pull any talks that belong to this Summit and the member is listed as a speaker
        $speaker = Speaker::get()->filter('memberID', $memberID)->first();
        if ($speaker) {
            $talksMemberIsASpeaker = $speaker->TalksBySummitID($this->ID);

            // Now merge and de-dupe the lists
            $SpeakerList->merge($talksMemberIsASpeaker);
            $SpeakerList->removeDuplicates('ID');
        }

        return $SpeakerList;
    }

    /*
     * @return int
     */
    public static function CurrentSummitID()
    {
        $current = self::CurrentSummit();
        return is_null($current) ? 0 : $current->ID;
    }

    /**
     * @return DataObject
     */
    public static function CurrentSummit()
    {
        $now = new \DateTime('now', new DateTimeZone('UTC'));
        return Summit::get()->filter(array(
            'SummitBeginDate:LessThanOrEqual' => $now->format('Y-m-d H:i:s'),
            'SummitEndDate:GreaterThanOrEqual' => $now->format('Y-m-d H:i:s'),
            'Active' => 1
        ))->first();
    }

    /**
     * @return bool
     */
    public function IsCurrent()
    {
        $now = new \DateTime('now', new DateTimeZone('UTC'));
        $start = new \DateTime($this->SummitBeginDate, new DateTimeZone('UTC'));
        $end = new \DateTime($this->SummitEndDate, new DateTimeZone('UTC'));
        return $this->Active && $start <= $now && $end >= $now;
    }

    public function IsUpComing()
    {
        $now = new \DateTime('now', new DateTimeZone('UTC'));
        $start = new \DateTime($this->SummitBeginDate, new DateTimeZone('UTC'));
        $end = new \DateTime($this->SummitEndDate, new DateTimeZone('UTC'));
        return $this->Active && $start >= $now && $end >= $now;
    }

    public static function GetUpcoming()
    {
        $now = new \DateTime('now', new DateTimeZone('UTC'));
        return Summit::get()->filter(array(
            'SummitBeginDate:GreaterThanOrEqual' => $now->format('Y-m-d H:i:s'),
            'SummitEndDate:GreaterThanOrEqual' => $now->format('Y-m-d H:i:s'),
            'Active' => 1
        ))->first();
    }


    public function onAfterWrite()
    {
        parent::onAfterWrite();

        if ($this->Active) {
            foreach (Presentation::get()->exclude('ID', $this->ID) as $p) {
                $p->Active = false;
                $p->write();
            }
        }
    }


    // CMS admin UI


    public function getCMSFields()
    {

        $f = new FieldList(
            $rootTab = new TabSet("Root",   $tabMain = new Tab('Main'))
        );

        $f->addFieldToTab('Root.Main',new TextField('Title','Title'));
        $f->addFieldToTab('Root.Main',new TextField('Location','Location'));
        $f->addFieldToTab('Root.Main',$link = new TextField('Link','Summit Page Link'));
        $link->setDescription('The link to the site page for this summit. Eg: <em>/summit/vancouver-2015/</em>');
        $f->addFieldToTab('Root.Main',new CheckboxField('Active','This is the active summit'));
        $f->addFieldToTab('Root.Main',$date_label = new TextField('DateLabel','Date label'));
        $date_label->setDescription('A readable piece of text representing the date, e.g. <em>May 12-20, 2015</em> or <em>December 2016</em>');

        $f->addFieldToTab('Root.Main',$registration_link = new TextField('RegistrationLink', 'Registration Link'));
        $registration_link->setDescription('Link to the site where tickets can be purchased.');

        $f->addFieldToTab('Root.Main',$date = new DatetimeField('SummitBeginDate', 'Summit Begin Date'));
        $date->setConfig('showcalendar', true);
        $f->addFieldToTab('Root.Main',$date = new DatetimeField('SummitEndDate', 'Summit End Date'));
        $date->setConfig('showcalendar', true);
        $f->addFieldToTab('Root.Main',$date = new DatetimeField('SubmissionBeginDate', 'Submission Begin Date'));
        $date->setConfig('showcalendar', true);
        $f->addFieldToTab('Root.Main',$date = new DatetimeField('SubmissionEndDate', 'Submission End Date'));
        $date->setConfig('showcalendar', true);
        $f->addFieldToTab('Root.Main',$date = new DatetimeField('VotingBeginDate', 'Voting Begin Date'));
        $date->setConfig('showcalendar', true);
        $f->addFieldToTab('Root.Main',$date = new DatetimeField('VotingEndDate', 'Voting End Date'));
        $date->setConfig('showcalendar', true);
        $f->addFieldToTab('Root.Main',$date = new DatetimeField('SelectionBeginDate', 'Selection Begin Date'));
        $date->setConfig('showcalendar', true);
        $f->addFieldToTab('Root.Main',$date = new DatetimeField('SelectionEndDate', 'Selection End Date'));
        $date->setConfig('showcalendar', true);

        $f->addFieldToTab('Root.Main',new TextField('ComingSoonBtnText', 'Coming Soon Btn Text'));

        $config = new GridFieldConfig_RelationEditor(10);
        $categories = new GridField('Categories','Categories',$this->Categories(), $config);
        $f->addFieldToTab('Root.Categories', $categories);

        return $f;
    }

}