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

    static $db = array(
        'Name'                        => 'Varchar(255)',
        'Location'                    => 'Varchar(255)',
        'StartDate'                   => 'Date',
        'EndDate'                     => 'Date',
        'SummitBeginDate'             => 'Date',
        'SummitEndDate'               => 'Date',
        'AcceptSubmissionsStartDate'  => 'Date',
        'AcceptSubmissionsEndDate'    => 'Date',
        'VoteForSubmissionsStartDate' => 'Date',
        'VoteForSubmissionsEndDate'   => 'Date',
        'DateLabel'                   => 'Varchar',
        'Link'                        => 'Varchar',
        'RegistrationLink'            => 'Text',
        'Active'                      => 'Boolean',
        'ComingSoonBtnText'           => 'Text',
    );

    static $has_many = array(
        'Talks' => 'Talk',
        'Tracks' => 'SummitTrack',
        'Rooms' => 'SummitRoom',
        'TimeSlots' => 'SummitTimeSlot',
        'SummitCategories' => 'SummitCategory'
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
        $endField = "{$key}EndDate";

        if (!$this->hasField($beginField) || !$this->hasField($endField)) return false;

        return (time() > $this->obj($beginField)->format('U')) && (time() < $this->obj($endField)->format('U'));
    }


    public function getStatus()
    {
        if (!$this->Active) return "INACTIVE";

        return "DRAFT";
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
       return is_null($current)?0:$current->ID;
    }

    /**
     * @return DataObject
     */
    public static function CurrentSummit()
    {
        $now   = new \DateTime('now', new DateTimeZone('UTC'));
        return Summit::get()->filter(array(
            'StartDate:LessThanOrEqual'  =>  $now->format('Y-m-d H:i:s'),
            'EndDate:GreaterThanOrEqual' =>  $now->format('Y-m-d H:i:s'),
            'Active'  => 1
        ))->first();
    }

    /**
     * @return bool
     */
    public function IsCurrent(){
        $now   = new \DateTime('now', new DateTimeZone('UTC'));
        $start = new \DateTime($this->StartDate, new DateTimeZone('UTC'));
        $end   = new \DateTime($this->EndDate, new DateTimeZone('UTC'));
        return $this->Active && $start <= $now && $end >= $now;
    }

    public function IsUpComing(){
        $now   = new \DateTime('now', new DateTimeZone('UTC'));
        $start = new \DateTime($this->StartDate, new DateTimeZone('UTC'));
        $end   = new \DateTime($this->EndDate, new DateTimeZone('UTC'));
        return $this->Active && $start >= $now && $end >= $now;
    }

    public static function GetUpcoming(){
        $now   = new \DateTime('now', new DateTimeZone('UTC'));
        return Summit::get()->filter(array(
            'StartDate:GreaterThanOrEqual'  =>  $now->format('Y-m-d H:i:s'),
            'EndDate:GreaterThanOrEqual' =>  $now->format('Y-m-d H:i:s'),
            'Active'  => 1
        ))->first();
    }

}