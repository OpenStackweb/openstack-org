<?php
/**
 * Copyright 2017 OpenStack Foundation
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

PublisherSubscriberManager::getInstance()->subscribe(ISummitEntityEvent::UpdatedEntity, function($entity){
    $metadata = '';

    $summit_id = isset($_REQUEST['SummitID']) ? intval($_REQUEST['SummitID']) : $entity->getField("SummitID");
    if(is_null($summit_id) || $summit_id == 0) $summit_id = Summit::ActiveSummitID();
    if($entity instanceof PresentationMaterial){
        $summit_id = $entity->Presentation()->SummitID;
    }
    $entity_class_name = $entity->ClassName;

    if($entity instanceof SummitEvent)
    {
        $fields = $entity->getChangedFields(true);
        // check if there was a change on publishing state
        $type = ICalendarSyncWorkRequest::TypeUpdate;
        if(isset($fields['Published']))
        {
            $pub_old  = intval($fields['Published']['before']);
            $pub_new  = intval($fields['Published']['after']);
            $metadata = json_encode( array ( 'pub_old' => $pub_old, 'pub_new' => $pub_new));
            if($pub_old == 1 && $pub_new == 0) $type = ICalendarSyncWorkRequest::TypeRemove;
        }
        else
        {
            // just record the published state at the moment of the update
            $metadata = json_encode( array ( 'pub_new' => intval($entity->Published)));
        }

        $request = new AdminSummitEventActionSyncWorkRequest();
        $request->SummitEventID = $entity->ID;
        $request->Type = $type;
        $request->CreatedByID = Member::currentUserID();
        $request->write();
    }

    if($entity instanceof SummitAbstractLocation && $entity->hasPublishedEvents()){
        $request = new AdminSummitLocationActionSyncWorkRequest();
        $request->LocationID = $entity->ID;
        $request->Type = ICalendarSyncWorkRequest::TypeUpdate;
        $request->CreatedByID = Member::currentUserID();
        $request->write();
    }

    if($entity instanceof SummitVenueFloor){
        $metadata = json_encode(array('venue_id' => intval($entity->VenueID)));
    }

    if($entity instanceof PresentationMaterial){
        $metadata = json_encode(array('presentation_id' => intval($entity->PresentationID)));
    }

    if($entity instanceof SummitVenueRoom){
        $fields = $entity->getChangedFields(true);
        if(isset($fields['FloorID']))
        {
            $floor_old         = intval($fields['FloorID']['before']);
            $floor_new         = intval($fields['FloorID']['after']);
            $metadata          = json_encode( array ( 'floor_old' => $floor_old, 'floor_new' => $floor_new));
        }
    }

    if($entity instanceof SummitLocationImage || $entity instanceof SummitLocationMap){
        $metadata = json_encode(array('location_id' => intval($entity->LocationID)));
    }

    if($entity instanceof CalendarSyncInfo){

        $fields = $entity->getChangedFields(true);
        if(isset($fields['Revoked'])) {
            $revoked_old  = intval($fields['Revoked']['before']);
            $revoked_new  = intval($fields['Revoked']['after']);
            if($revoked_old == 0 && $revoked_new == 1) {
                // revoked credentials => delete calendar
                $current_member                          = Member::currentUser();
                $summit                                  = Summit::get()->byID($summit_id);
                $create_cal_request = new MemberCalendarScheduleSummitActionSyncWorkRequest();
                $create_cal_request->OwnerID = $current_member->ID;
                $create_cal_request->CalendarSyncInfoID = $entity->ID;
                $create_cal_request->Type = ICalendarSyncWorkRequest::TypeRemove;
                $create_cal_request->CalendarId = $summit->getCalendarSyncId();
                $create_cal_request->CalendarName = $summit->getCalendarSyncName();
                $create_cal_request->CalendarDescription = $summit->getCalendarSyncDescription();
                $create_cal_request->write();
            }
        }
        return;
    }

    $event                  = new SummitEntityEvent();
    $event->EntityClassName = $entity_class_name;
    $event->EntityID        = $entity->ID;
    $event->Type            = 'UPDATE';
    $event->OwnerID         = Member::currentUserID();
    $event->SummitID        = $summit_id;
    $event->Metadata        = $metadata;
    $event->write();
});

PublisherSubscriberManager::getInstance()->subscribe(ISummitEntityEvent::InsertedEntity, function($entity){

    $metadata = '';
    $summit_id = isset($_REQUEST['SummitID']) ? intval($_REQUEST['SummitID']) : $entity->getField("SummitID");
    if(is_null($summit_id) || $summit_id == 0) $summit_id = Summit::ActiveSummitID();
    if($entity instanceof PresentationMaterial){
        $summit_id = $entity->Presentation()->SummitID;
    }

    if($entity instanceof SummitEvent)
    {
        $pub_new  = intval($entity->Published);
        $metadata = json_encode( array ('pub_new' => $pub_new));
    }

    if($entity instanceof SummitVenueFloor){
        $metadata = json_encode(array('venue_id' => intval($entity->VenueID)));
    }

    if($entity instanceof SummitLocationImage || $entity instanceof SummitLocationMap){
        $metadata = json_encode(array('location_id' => intval($entity->LocationID)));
    }

    if($entity instanceof PresentationMaterial){
        $metadata = json_encode(array('presentation_id' => intval($entity->PresentationID)));
    }

    if($entity instanceof CalendarSyncInfo){
        // create calendar ...
        $current_member                          = Member::currentUser();
        $summit                                  = Summit::get()->byID($summit_id);
        $create_cal_request                      = new MemberCalendarScheduleSummitActionSyncWorkRequest();
        $create_cal_request->OwnerID             = $current_member->ID;
        $create_cal_request->CalendarSyncInfoID  = $entity->ID;
        $create_cal_request->Type                = ICalendarSyncWorkRequest::TypeAdd;
        $create_cal_request->CalendarId          = $summit->getCalendarSyncId();
        $create_cal_request->CalendarName        = $summit->getCalendarSyncName();
        $create_cal_request->CalendarDescription = $summit->getCalendarSyncDescription();
        $create_cal_request->write();
        $schedule_event_ids = $current_member->getScheduleEventIds($summit_id);

        // if we have former scheduled events ...
        // then create all request to sync event for new calendar

        foreach($schedule_event_ids as $event_id){
            $event_sync_request                      = new MemberEventScheduleSummitActionSyncWorkRequest();
            $event_sync_request->OwnerID             = $current_member->ID;
            $event_sync_request->CalendarSyncInfoID  = $entity->ID;
            $event_sync_request->SummitEventID       = intval($event_id);
            $event_sync_request->Type                = ICalendarSyncWorkRequest::TypeAdd;
            $event_sync_request->write();
        }

        return;
    }

    $event                  = new SummitEntityEvent();
    $event->EntityClassName = $entity->ClassName;
    $event->EntityID        = $entity->ID;
    $event->Type            = 'INSERT';
    $event->OwnerID         = Member::currentUserID();
    $event->SummitID        = $summit_id;
    $event->Metadata        = $metadata;
    $event->write();
});

PublisherSubscriberManager::getInstance()->subscribe(ISummitEntityEvent::DeletedEntity, function($entity){

    $metadata = '';
    $summit_id = isset($_REQUEST['SummitID']) ? intval($_REQUEST['SummitID']) : $entity->getField("SummitID");
    if(is_null($summit_id) || $summit_id == 0) $summit_id = Summit::ActiveSummitID();
    if($entity instanceof PresentationMaterial)
    {
        $summit_id = $entity->Presentation()->SummitID;
    }

    if($entity instanceof SummitEvent && $entity->isPublished())
    {
        // just record the published state at the moment of the update
        $metadata = json_encode([
            'pub_old' => intval($entity->Published),
            'pub_new' => intval($entity->Published)
        ]);

        $request = new AdminSummitEventActionSyncWorkRequest();
        $request->SummitEventID = $entity->ID;
        $request->Type = ICalendarSyncWorkRequest::TypeRemove;
        $request->CreatedByID = Member::currentUserID();
        $request->write();
    }

    if($entity instanceof SummitAbstractLocation){
        // check if we have published events...

        if($entity->hasPublishedEvents()){
            $request                  = new AdminSummitLocationActionSyncWorkRequest();
            $request->LocationID      = $entity->ID;
            $request->Type            = ICalendarSyncWorkRequest::TypeRemove;
            $request->CreatedByID     = Member::currentUserID();
            $request->write();
        }
    }

    if($entity instanceof SummitVenueFloor){
        $metadata = json_encode(array('venue_id' => intval($entity->VenueID)));
    }

    if($entity instanceof SummitLocationImage || $entity instanceof SummitLocationMap){
        $metadata = json_encode(array('location_id' => intval($entity->LocationID)));
    }

    if($entity instanceof PresentationMaterial){
        $metadata = json_encode(array('presentation_id' => intval($entity->PresentationID)));
    }

    $event                  = new SummitEntityEvent();
    $event->EntityClassName = $entity->ClassName;
    $event->EntityID        = $entity->ID;
    $event->Type            = 'DELETE';
    $event->OwnerID         = Member::currentUserID();
    $event->SummitID        = $summit_id;
    $event->Metadata        = $metadata;
    $event->write();
});

PublisherSubscriberManager::getInstance()->subscribe(ISummitEntityEvent::AddedToSchedule, function($member, $summit_event){

    $summit_id              = $summit_event->getSummit()->ID;
    $metadata               = '';

    $event                  = new SummitEntityEvent();
    $event->EntityClassName = 'MySchedule';
    $event->EntityID        = $summit_event->ID;
    $event->Type            = 'INSERT';
    $event->OwnerID         = $member->ID;
    $event->SummitID        = $summit_id;
    $event->Metadata        = $metadata;
    $event->write();

    // work request
    $sync_info = $member->getCalendarSyncInfoBy($summit_id);

    if(!is_null($sync_info)){
        $request                     = new MemberEventScheduleSummitActionSyncWorkRequest();
        $request->Type               = ICalendarSyncWorkRequest::TypeAdd;
        $request->OwnerID            = $member->ID;
        $request->CalendarSyncInfoID = $sync_info->ID;
        $request->SummitEventID      = $summit_event->ID;
        $request->write();
    }

});

PublisherSubscriberManager::getInstance()->subscribe(ISummitEntityEvent::RemovedFromSchedule, function($member, $summit_event){

    $summit_id              = $summit_event->getSummit()->ID;
    $metadata               = '';
    $event                  = new SummitEntityEvent();
    $event->EntityClassName = 'MySchedule';
    $event->EntityID        = $summit_event->ID;
    $event->Type            = 'DELETE';
    $event->OwnerID         = $member->ID;
    $event->SummitID        = $summit_id;
    $event->Metadata        = $metadata;
    $event->write();

    // work request
    $sync_info = $member->getCalendarSyncInfoBy($summit_id);

    if(!is_null($sync_info)){

        $request = new MemberEventScheduleSummitActionSyncWorkRequest();
        $request->Type = ICalendarSyncWorkRequest::TypeRemove;
        $request->OwnerID = $member->ID;
        $request->CalendarSyncInfoID = $sync_info->ID;
        $request->SummitEventID = $summit_event->ID;
        $request->write();
    }
});

PublisherSubscriberManager::getInstance()->subscribe(ISummitEntityEvent::AddedToFavorites, function($member, $summit_event){

    $summit_id              = $summit_event->getSummit()->ID;
    $metadata               = '';
    $event                  = new SummitEntityEvent();
    $event->EntityClassName = 'MyFavorite';
    $event->EntityID        = $summit_event->ID;
    $event->Type            = 'INSERT';
    $event->OwnerID         = $member->ID;
    $event->SummitID        = $summit_id;
    $event->Metadata        = $metadata;
    $event->write();
});

PublisherSubscriberManager::getInstance()->subscribe(ISummitEntityEvent::RemovedFromFavorites, function($member, $summit_event){

    $summit_id              = $summit_event->getSummit()->ID;
    $metadata               = '';
    $event                  = new SummitEntityEvent();
    $event->EntityClassName = 'MyFavorite';
    $event->EntityID        = $summit_event->ID;
    $event->Type            = 'DELETE';
    $event->OwnerID         = $member->ID;
    $event->SummitID        = $summit_id;
    $event->Metadata        = $metadata;
    $event->write();
});

PublisherSubscriberManager::getInstance()->subscribe('manymanylist_added_item', function($list, $item){
    if($item instanceof ISummitEvent && $list->getJoinTable() === 'Member_Schedule') {
        $summit_id = $item->getField("SummitID");
        if (is_null($summit_id) || $summit_id == 0) {
            $summit_id = Summit::ActiveSummitID();
        }

        $metadata = '';
        $event = new SummitEntityEvent();
        $event->EntityClassName = 'MySchedule';
        $event->EntityID = $item->ID;
        $event->Type = 'INSERT';
        $event->OwnerID = Member::currentUserID();
        $event->SummitID = $summit_id;
        $event->Metadata = $metadata;
        $event->write();
    }
    if($item instanceof Company && $list->getJoinTable() === 'SummitEvent_Sponsors')
    {
        // add sponsor
        $event_id     = intval($list->getForeignID());
        $summit_event        = SummitEvent::get()->byID($event_id);
        if(is_null($summit_event)) return;
        $company_id      = $item->ID;
        $event = new SummitEntityEvent();
        $event->EntityClassName = 'SponsorFromEvent';
        $event->EntityID = $company_id;
        $event->Type     = 'INSERT';
        $event->OwnerID  = Member::currentUserID();
        $event->SummitID = $summit_event->SummitID;
        $event->Metadata = json_encode( array('event_id' => $event_id ));
        $event->write();
    }
    if($item instanceof PresentationSpeaker && $list->getJoinTable() === 'Presentation_Speakers')
    {
        // add speaker from presentation

        $presentation_id = intval($list->getForeignID());
        $presentation    = Presentation::get()->byID($presentation_id);
        if(is_null($presentation)) return;
        $speaker_id      = $item->ID;
        $event = new SummitEntityEvent();
        $event->EntityClassName = 'SpeakerFromPresentation';
        $event->EntityID = $speaker_id;
        $event->Type     = 'INSERT';
        $event->OwnerID  = Member::currentUserID();
        $event->SummitID = $presentation->SummitID;
        $event->Metadata = json_encode( array('event_id' => $presentation_id ));
        $event->write();
    }
    if($item instanceof PresentationCategory && $list->getJoinTable() === 'PresentationCategoryGroup_Categories')
    {
        $group_id  = intval($list->getForeignID());
        $group     = PresentationCategoryGroup::get()->byID($group_id);
        if(is_null($group)) return;
        $track_id  = $item->ID;
        $event = new SummitEntityEvent();
        $event->EntityClassName = 'TrackFromTrackGroup';
        $event->EntityID = $track_id;
        $event->Type     = 'INSERT';
        $event->OwnerID  = Member::currentUserID();
        $event->SummitID = $group->SummitID;
        $event->Metadata = json_encode( array('group_id' => $group_id ));
        $event->write();
    }
    if($item instanceof ISummitEvent && $list->getJoinTable() === 'Member_FavoriteSummitEvents') {
        $summit_id = $item->getField("SummitID");
        if (is_null($summit_id) || $summit_id == 0) {
            $summit_id = Summit::ActiveSummitID();
        }

        $member_id = $list->getForeignID();

        $metadata = '';
        $event = new SummitEntityEvent();
        $event->EntityClassName = 'MyFavorite';
        $event->EntityID = $item->ID;
        $event->Type = 'INSERT';
        $event->OwnerID =$member_id;
        $event->SummitID = $summit_id;
        $event->Metadata = $metadata;
        $event->write();
    }
});

PublisherSubscriberManager::getInstance()->subscribe('manymanylist_removed_item', function($list, $item){

    if($item instanceof ISummitEvent && $list->getJoinTable() === 'Member_Schedule') {
        $summit_id = $item->getField("SummitID");
        if (is_null($summit_id) || $summit_id == 0) {
            $summit_id = Summit::ActiveSummitID();
        }
        $metadata = '';
        $event = new SummitEntityEvent();
        $event->EntityClassName = 'MySchedule';
        $event->EntityID        = $item->ID;
        $event->Type            = 'DELETE';
        $event->OwnerID         = Member::currentUserID();
        $event->SummitID        = $summit_id;
        $event->Metadata        = $metadata;
        $event->write();
    }
    if($item instanceof ISummitEvent && $list->getJoinTable() === 'Member_FavoriteSummitEvents') {
        $summit_id = $item->getField("SummitID");
        if (is_null($summit_id) || $summit_id == 0) {
            $summit_id = Summit::ActiveSummitID();
        }

        $metadata = '';
        $event = new SummitEntityEvent();
        $event->EntityClassName = 'MyFavorite';
        $event->EntityID = $item->ID;
        $event->Type     = 'DELETE';
        $event->OwnerID  = Member::currentUserID();
        $event->SummitID = $summit_id;
        $event->Metadata = $metadata;
        $event->write();
    }
    if($item instanceof Company && $list->getJoinTable() === 'SummitEvent_Sponsors')
    {
        // removed sponsor
        $event_id     = intval($list->getForeignID());
        $summit_event = SummitEvent::get()->byID($event_id);
        if(is_null($summit_event)) return;
        $company_id      = $item->ID;
        $event = new SummitEntityEvent();
        $event->EntityClassName = 'SponsorFromEvent';
        $event->EntityID = $company_id;
        $event->Type     = 'DELETE';
        $event->OwnerID  = Member::currentUserID();
        $event->SummitID = $summit_event->SummitID;
        $event->Metadata = json_encode( array('event_id' => $event_id ));
        $event->write();
    }
    if($item instanceof PresentationSpeaker && $list->getJoinTable() === 'Presentation_Speakers')
    {
        // removed speaker from presentation

        $presentation_id = intval($list->getForeignID());
        $presentation    = Presentation::get()->byID($presentation_id);
        if(is_null($presentation)) return;
        $speaker_id      = $item->ID;
        $event = new SummitEntityEvent();
        $event->EntityClassName = 'SpeakerFromPresentation';
        $event->EntityID = $speaker_id;
        $event->Type     = 'DELETE';
        $event->OwnerID  = Member::currentUserID();
        $event->SummitID = $presentation->SummitID;
        $event->Metadata = json_encode( array('event_id' => $presentation_id ));
        $event->write();
    }
    if($item instanceof PresentationCategory && $list->getJoinTable() === 'PresentationCategoryGroup_Categories')
    {
        $group_id  = intval($list->getForeignID());
        $group     = PresentationCategoryGroup::get()->byID($group_id);
        if(is_null($group)) return;
        $track_id  = $item->ID;
        $event = new SummitEntityEvent();
        $event->EntityClassName = 'TrackFromTrackGroup';
        $event->EntityID = $track_id;
        $event->Type     = 'DELETE';
        $event->OwnerID  = Member::currentUserID();
        $event->SummitID = $group->SummitID;
        $event->Metadata = json_encode( array('group_id' => $group_id ));
        $event->write();
    }
});

define('SUMMIT_ATTENDEE_CREATE_MEMBERSHIP_INVITATION_EMAIL_TEMPLATE', 'summit-attendee-create-membership-invitation');
define('SUMMIT_ATTENDEE_CREATED_EMAIL_TEMPLATE', 'summit-attendee-created');

// speaker promo code ingestion configuration
// email templates ids

define('PRESENTATION_SPEAKER_ACCEPTED_ONLY_EMAIL', 'presentation-speaker-accepted-only');
define('PRESENTATION_SPEAKER_ALTERNATE_ONLY_EMAIL', 'presentation-speaker-alternate-only');
define('PRESENTATION_SPEAKER_REJECTED_EMAIL', 'presentation-speaker-rejected-only');

define('PRESENTATION_SPEAKER_ACCEPTED_REJECTED_EMAIL', 'presentation-speaker-accepted-rejected');
define('PRESENTATION_SPEAKER_ALTERNATE_REJECTED_EMAIL', 'presentation-speaker-alternate-rejected');
define('PRESENTATION_SPEAKER_ACCEPTED_ALTERNATE_EMAIL','presentation-speaker-accepted-alternate');


define('PRESENTATION_SPEAKER_NOTIFICATION_ACCEPTANCE_EMAIL_FROM', 'speakersupport@openinfra.dev');
define('PRESENTATION_SPEAKER_NOTIFICATION_ACCEPTANCE_SUMMIT_SUPPORT', 'summit@openstack.org');

define('MEMBER_PROMO_CODE_EMAIL', 'member-promo-code');
define('MEMBER_NOTIFICATION_PROMO_CODE_EMAIL_FROM', 'speakersupport@openstack.org');
define('SUMMIT_ATTENDEE_RSVP_EMAIL','summit-attendee-rsvp');
define('SUMMIT_ATTENDEE_RSVP_WAITLIST_EMAIL','summit-attendee-rsvp-waitlist');
define('SUMMIT_ATTENDEE_RSVP_EMAIL_FROM','summit@openstack.org');

// Second Break Out Email Templates
define('PRESENTATION_SPEAKER_CREATE_MEMBERSHIP_EMAIL', 'presentation-speaker-create-membership-email');
define('PRESENTATION_SPEAKER_CONFIRM_SUMMIT_ASSISTANCE_EMAIL', 'presentation-speaker-confirm-assistance-email');
define('PRESENTATION_SPEAKER_SUMMIT_REMINDER_EMAIL', 'presentation-speaker-summit-reminder-email');

// Summit Calendar Sync error emails
define("SUMMIT_CALENDAR_SYNC_ERROR_EMAIL_TEMPLATE", "summit-calendar-sync-error");
define("SUMMIT_CALENDAR_SYNC_ERROR_EMAIL_SUBJECT", "OpenStack Summit - Relink your %s calendar account with OpenStack Summit %s Calendar");