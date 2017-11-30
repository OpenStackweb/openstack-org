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
final class AttendeeMember extends DataExtension implements IAttendeeMember
{
    private static $has_many = [
        'SummitAttendance'          => 'SummitAttendee',
        'RSVPs'                     => 'RSVP',
        'CalendarSync'              => 'CalendarSyncInfo',
        'ScheduleSync'              => 'ScheduleCalendarSyncInfo',
        'SummitRegistrationCodes'   => 'MemberSummitRegistrationPromoCode',
    ];

    private static $many_many = [
        'FavoriteSummitEvents'  => 'SummitEvent',
        'Schedule'              => 'SummitEvent',
    ];



    /**
     * @param int|null $summit_id
     * @return bool
     */
    public function isAttendee($summit_id = null)
    {
        $attendee = $this->getSummitAttendee($summit_id);

        return !is_null($attendee);
    }

    /**
     * @param int|null $summit_id
     * @return ISummitAttendee
     */
    public function getSummitAttendee($summit_id = null)
    {
        $attendees = $this->owner->SummitAttendance();
        if ($attendees->Count() > 0) {
            if (!is_null($summit_id)) {
                $summit_attendees = $attendees->filter('SummitID', $summit_id);
                $attendee = $summit_attendees->first();
            } else {
                $attendee = $attendees->first();
            }

            return $attendee;
        } else {
            return null;
        }
    }

    /**
     * @param int $summit_id
     * @return CalendarSyncInfo|null
     */
    public function getCalendarSyncInfoBy($summit_id){
        return $this->owner->CalendarSync()->filter
        ([
            'SummitID' => $summit_id,
            'Revoked'  => false,
         ]
        )->first();
    }

    /**
     * @return int
     */
    public function getIdentifier()
    {
        return (int)$this->owner->getField('ID');
    }

    /**
     * @return ISummitAttendee|null
     */
    public function getCurrentSummitAttendee()
    {
        $current_summit = Summit::CurrentSummit();
        if ($current_summit) {
            return $this->getSummitAttendee($current_summit->ID);
        }

        return $this->getUpcomingSummitAttendee();
    }

    /**
     * @return ISummitAttendee|null
     */
    public function getUpcomingSummitAttendee()
    {
        $upcoming_summit = Summit::ActiveSummit();
        if ($upcoming_summit) {
            return $this->getSummitAttendee($upcoming_summit->ID);
        }

        return null;
    }

    public function onBeforeDelete()
    {
        // Remove attendees
        foreach ($this->owner->SummitAttendance() as $attendee) {
            $attendee->delete();
        }


        $favorites = $this->owner->getManyManyComponents('FavoriteSummitEvents');
        $favorites->removeAll();

        $schedule = $this->owner->getManyManyComponents('Schedule');
        $schedule->removeAll();

        foreach ($this->owner->RSVPs() as $rsvp) {
            $rsvp->delete();
        }
    }

    public function updateCMSFields(FieldList $fields)
    {
        $fields->removeByName("FavoriteSummitEvents");
        $fields->removeByName("Schedule");

        // schedule
        $config = GridFieldConfig_RelationEditor::create(50);
        $config->removeComponentsByType('GridFieldAddNewButton');
        $config->getComponentByType('GridFieldAddExistingAutocompleter')->setSearchList($this->getAllowedSchedule());
        $config->addComponent(new GridFieldAjaxRefresh(1000,false));
        $detailFormFields = new FieldList();
        /*$detailFormFields->push(new CheckBoxField(
            'ManyMany[IsCheckedIn]',
            'Is Checked In?'
        ));*/
        $config->getComponentByType('GridFieldDetailForm')->setFields($detailFormFields);
        $gridField = new GridField('Schedule', 'Schedule', $this->owner->Schedule(), $config);
        $fields->addFieldToTab('Root.Schedule', $gridField);
        // favorites
        $config = GridFieldConfig_RelationEditor::create(50);
        $config->removeComponentsByType('GridFieldAddNewButton');
        $config->getComponentByType('GridFieldAddExistingAutocompleter')->setSearchList($this->getAllowedSchedule());
        $config->addComponent(new GridFieldAjaxRefresh(1000,false));
        $gridField = new GridField('Favorites', 'Favorites', $this->owner->FavoriteSummitEvents(), $config);
        $fields->addFieldToTab('Root.Schedule', $gridField);
    }

    /**
     *  Favorites events
     */

    /**
     * @param int $event_id
     * @return bool
     */
    public function isOnFavorites($event_id){
        $member_id =  $this->owner->ID;
 $query = <<<SQL
  SELECT COUNT(Member_FavoriteSummitEvents.ID) FROM Member_FavoriteSummitEvents 
INNER JOIN SummitEvent ON SummitEvent.ID = Member_FavoriteSummitEvents.SummitEventID
AND MemberID = $member_id
AND SummitEvent.ID = $event_id
AND SummitEvent.Published = 1 ;
SQL;

        $res = intval(DB::query($query)->value());
        return $res  > 0;
    }

    /**
     * @param SummitEvent $event
     */
    public function addToFavorites(SummitEvent $event){
        AssociationFactory::getInstance()->getMany2ManyAssociation($this->owner, 'FavoriteSummitEvents')->add
        (
            $event,
            []
        );

        PublisherSubscriberManager::getInstance()->publish(ISummitEntityEvent::AddedToFavorites,
            [$this->owner, $event]);
    }

    /**
     * @param SummitEvent $event
     */
    public function removeFromFavorites(SummitEvent $event){
        AssociationFactory::getInstance()->getMany2ManyAssociation($this->owner, 'FavoriteSummitEvents')->remove($event);

        PublisherSubscriberManager::getInstance()->publish(ISummitEntityEvent::RemovedFromFavorites,
            [$this->owner, $event]);
    }

    /**
     * @param int $summit_id
     * @return int[]
     */
    public function getFavoritesEventIds($summit_id){
        $res   = [];
        $query = DB::query("SELECT SummitEventID FROM Member_FavoriteSummitEvents 
INNER JOIN SummitEvent ON SummitEvent.ID = Member_FavoriteSummitEvents.SummitEventID
WHERE SummitEvent.SummitID = {$summit_id} AND MemberID = ".$this->owner->ID ." AND SummitEvent.Published = 1 ");
        foreach ($query as $record){
            $res[] = intval($record['SummitEventID']);
        }
        return $res;
    }

    /**
     * @return ISummitEvent[]
     */
    public function getSchedule()
    {
        return AssociationFactory::getInstance()->getMany2ManyAssociation($this->owner, 'Schedule')->toArray();
    }

    /**
     * @param int $summit_id
     * @return int[]
     */
    public function getScheduleEventIds($summit_id){
        $res   = [];
        $query = DB::query("SELECT SummitEventID 
FROM Member_Schedule 
INNER JOIN SummitEvent ON SummitEvent.ID = Member_Schedule.SummitEventID
WHERE SummitEvent.SummitID = {$summit_id} AND MemberId = ".$this->owner->ID ." AND SummitEvent.Published = 1 ");
        foreach ($query as $record){
            $res[] = intval($record['SummitEventID']);
        }
        return $res;
    }

    /**
     * @param int $event_id
     * @return bool
     */
    public function isOnMySchedule($event_id)
    {
        $member_id =  $this->owner->ID;
        $query = <<<SQL
  SELECT COUNT(Member_Schedule.ID) FROM Member_Schedule
INNER JOIN SummitEvent ON SummitEvent.ID = Member_Schedule.SummitEventID
AND MemberID = $member_id
AND SummitEvent.ID = $event_id
AND SummitEvent.Published = 1;
SQL;
        $res = intval(DB::query($query)->value());
        return $res  > 0;
    }

    /**
     * @param int $summit_id
     * @param array $sort
     * @return ArrayList
     */
    public function getScheduleBySummit($summit_id, array $sort = []){
        $member_id =  $this->owner->ID;
        $sort_by     = '';
        if(count($sort) > 0){
            foreach ($sort as $field => $order_dir){
                if(!empty($sort_by)) $sort_by .= ', ';
                $sort_by .= sprintf('%s %s', $field, $order_dir);
            }
        }
        if(!empty($sort_by)) $sort_by = ' ORDER BY '. $sort_by;

        $query = <<<SQL
SELECT SummitEvent.* FROM Member_Schedule
INNER JOIN SummitEvent ON SummitEvent.ID = Member_Schedule.SummitEventID
LEFT JOIN SummitAbstractLocation AS Location ON Location.ID = SummitEvent.LocationID
WHERE
MemberID = $member_id
AND SummitEvent.SummitID = $summit_id
AND SummitEvent.Published = 1
{$sort_by};
SQL;
        $list = new ArrayList();
        foreach (DB::query($query) as $row){
            $list->add(new SummitEvent($row));
        }
        return $list;
    }

    /**
     * @param ISummitEvent $summit_event
     * @return void
     */
    public function addToSchedule(ISummitEvent $summit_event)
    {
        AssociationFactory::getInstance()->getMany2ManyAssociation($this->owner, 'Schedule')->add
        (
            $summit_event,
            []
        );

        PublisherSubscriberManager::getInstance()->publish(ISummitEntityEvent::AddedToSchedule,
            [$this->owner, $summit_event]);
    }

    /**
     * @param ISummitEvent $summit_event
     * @return void
     */
    public function removeFromSchedule(ISummitEvent $summit_event)
    {
        AssociationFactory::getInstance()->getMany2ManyAssociation($this->owner, 'Schedule')->remove($summit_event);

        PublisherSubscriberManager::getInstance()->publish(ISummitEntityEvent::RemovedFromSchedule,
            [$this->owner, $summit_event]);
    }

    /**
     * @return void
     */
    public function clearSchedule()
    {
        AssociationFactory::getInstance()->getMany2ManyAssociation($this->owner, 'Schedule')->removeAll();
    }

    /*
     * @param int $event_id
     * @return bool
     */
    public function hasRSVPSubmission($event_id)
    {
        $event = SummitEvent::get()->byID($event_id);
        if(!$event) return false;
        return $event->RSVPSubmissions()->filter('SubmittedByID', $this->owner->ID)->count() > 0;
    }

    /**
     * @return DataList
     */
    public function getAllowedSchedule()
    {
        $summit = Summit::get_active();
        if(is_null($summit)) $summit = Summit::get()->byID(intval($_REQUEST['SummitID']));
        return SummitEvent::get()->filter(array('Published'=> true, 'SummitID' => $summit->ID));
    }


    /**
     * @param int $summit_id
     * @param array $sort
     * @return ArrayList
     */
    public function getFavoritesBySummit($summit_id, array $sort = [])
    {
        $member_id =  $this->owner->ID;
        $sort_by     = '';
        if(count($sort) > 0){
            foreach ($sort as $field => $order_dir){
                if(!empty($sort_by)) $sort_by .= ', ';
                $sort_by .= sprintf('%s %s', $field, $order_dir);
            }
        }
        if(!empty($sort_by)) $sort_by = ' ORDER BY '. $sort_by;

        $query = <<<SQL
SELECT SummitEvent.* FROM Member_FavoriteSummitEvents
INNER JOIN SummitEvent ON SummitEvent.ID = Member_FavoriteSummitEvents.SummitEventID
LEFT JOIN SummitAbstractLocation AS Location ON Location.ID = SummitEvent.LocationID
WHERE
MemberID = $member_id
AND SummitEvent.SummitID = $summit_id
AND SummitEvent.Published = 1
{$sort_by};
SQL;
        $list = new ArrayList();
        foreach (DB::query($query) as $row){
            $list->add(new SummitEvent($row));
        }
        return $list;
    }

    /**
     * @param Summit $summit
     * @param string $access_token
     * @param string $refresh_token
     * @return CalendarSyncInfo
     * @throws EntityValidationException
     */
    public function registerGoogleAuthGrant(Summit $summit, $access_token, $refresh_token)
    {
        $sync_info = $this->buildCalendarSyncInfoOAuth2(ICalendarSyncInfo::ProviderGoogle, $summit, $access_token, $refresh_token);
        $sync_info->write();
        return $sync_info;
    }

    /**
     * @param Summit $summit
     * @param string $access_token
     * @param string $refresh_token
     * @return CalendarSyncInfo
     * @throws EntityValidationException
     */
    public function registerOutlookAuthGrant(Summit $summit, $access_token, $refresh_token)
    {
        $sync_info = $this->buildCalendarSyncInfoOAuth2(ICalendarSyncInfo::ProviderOutlook, $summit, $access_token, $refresh_token);
        $sync_info->write();
        return $sync_info;
    }

    /**
     * @param string $provider
     * @param Summit $summit
     * @param string $access_token
     * @param string $refresh_token
     * @return CalendarSyncInfoOAuth2
     * @throws EntityValidationException
     */
    private function buildCalendarSyncInfoOAuth2($provider, Summit $summit, $access_token, $refresh_token){
        if($this->existCalendarSyncInfoForProviderAndSummit($provider, $summit->getIdentifier()))
            throw new EntityValidationException
            (
                sprintf
                (
                    'sync calender info already exists for provider %s - summit %d - member %s',
                    $provider,
                    $summit->ID,
                    $this->owner->ID
                )
            );

        $enc                     = new Encrypter(Encrypter_Key,Encrypter_Cipher);
        $sync_info               = new CalendarSyncInfoOAuth2();
        $sync_info->Revoked      = false;
        $sync_info->OwnerID      = $this->owner->ID;
        $sync_info->SummitID     = $summit->ID;
        $sync_info->Provider     = $provider;
        $sync_info->AccessToken  = $enc->encrypt(trim($access_token));
        $sync_info->RefreshToken = $enc->encrypt(trim($refresh_token));
        return $sync_info;
    }

    /**
     * @param string $provider
     * @param int $summit_id
     * @return bool
     */
    public function existCalendarSyncInfoForProviderAndSummit($provider, $summit_id){
        return CalendarSyncInfo::get()->filter(
            [
                'Provider' => $provider,
                'SummitID' => $summit_id,
                'OwnerID'  => $this->owner->ID,
                'Revoked'  => false,
            ]
        )->count() > 0;
    }

    /**
     * @param int $summit_id
     * @return bool
     */
    public function existCalendarSyncInfoForSummit($summit_id){
        return CalendarSyncInfo::get()->filter(
                [
                    'SummitID' => $summit_id,
                    'OwnerID'  => $this->owner->ID,
                    'Revoked'  => false,
                ]
            )->count() > 0;
    }

    /**
     * @param Summit $summit
     * @param string $user
     * @param string $password
     * @param string $user_ppal_url
     * @return CalendarSyncInfo
     * @throws EntityValidationException
     */
    public function registerICloudAuthGrant(Summit $summit, $user, $password, $user_ppal_url)
    {
        if($this->existCalendarSyncInfoForProviderAndSummit( ICalendarSyncInfo::ProviderICloud, $summit->getIdentifier()))
            throw new EntityValidationException
            (
                sprintf
                (
                    'sync calender info already exists for provider %s - summit %d - member %s',
                    ICalendarSyncInfo::ProviderICloud,
                    $summit->ID,
                    $this->owner->ID
                )
            );

        $enc                         = new Encrypter(Encrypter_Key,Encrypter_Cipher);
        $sync_info                   = new CalendarSyncInfoCalDav();
        $sync_info->Revoked          = false;
        $sync_info->OwnerID          = $this->owner->ID;
        $sync_info->SummitID         = $summit->ID;
        $sync_info->Provider         = ICalendarSyncInfo::ProviderICloud;
        $sync_info->UserName         = trim($user);
        $sync_info->UserPrincipalURL = $user_ppal_url;
        $sync_info->UserPassword     = $enc->encrypt(trim($password));
        $sync_info->write();
        return $sync_info;
    }

    /**
     * @param SummitEvent $event
     * @return bool
     */
    public function isEventSynchronized(SummitEvent $event){
        $info = $this->owner->ScheduleSync()->filter([
            'SummitEvent.ID' =>  $event->getIdentifier()
        ])->first();
        return !is_null($info);
    }

    /**
     * @param int $summit_id
     * @return bool
     */
    public function revokeCalendarSyncInfoForSummit($summit_id){
        $calendar_sync_info = CalendarSyncInfo::get()->filter(
                [
                    'SummitID' => $summit_id,
                    'OwnerID'  => $this->owner->ID,
                    'Revoked'  => false,
                ]
            )->first();
        if(is_null($calendar_sync_info)) return false;
        $calendar_sync_info->Revoked = true;
        $calendar_sync_info->write();
        return true;
    }

}