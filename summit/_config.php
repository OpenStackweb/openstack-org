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

PublisherSubscriberManager::getInstance()->subscribe(ISummitEntityEvent::UpdatedEntity, function($entity){

    $summit_id = isset($_REQUEST['SummitID']) ? intval($_REQUEST['SummitID']) : $entity->getField("SummitID");
    if(is_null($summit_id) || $summit_id == 0) $summit_id = Summit::ActiveSummitID();

    $metadata = '';

    if($entity instanceof SummitEvent)
    {
        $fields = $entity->getChangedFields(true);
        // check if there was a change on publishing state
        if(isset($fields['Published']))
        {
            $pub_old  = intval($fields['Published']['before']);
            $pub_new  = intval($fields['Published']['after']);
            $metadata = json_encode( array ( 'pub_old' => $pub_old, 'pub_new' => $pub_new));
        }
        else
        {
            // just record the published state at the moment of the update
            $metadata = json_encode( array ( 'pub_new' => intval($entity->Published)));
        }
    }

    $event                  = new SummitEntityEvent();
    $event->EntityClassName = $entity->ClassName;
    $event->EntityID        = $entity->ID;
    $event->Type            = 'UPDATE';
    $event->OwnerID         = Member::currentUserID();
    $event->SummitID        = $summit_id;
    $event->Metadata        = $metadata;
    $event->write();
});

PublisherSubscriberManager::getInstance()->subscribe(ISummitEntityEvent::InsertedEntity, function($entity){

    $summit_id = isset($_REQUEST['SummitID']) ? intval($_REQUEST['SummitID']) : $entity->getField("SummitID");
    if(is_null($summit_id) || $summit_id == 0) $summit_id = Summit::ActiveSummitID();
    $metadata = '';

    if($entity instanceof SummitEvent)
    {
        $pub_new  = intval($entity->Published);
        $metadata = json_encode( array ('pub_new' => $pub_new));
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

    $summit_id = isset($_REQUEST['SummitID']) ? intval($_REQUEST['SummitID']) : $entity->getField("SummitID");
    if(is_null($summit_id) || $summit_id == 0) $summit_id = Summit::ActiveSummitID();
    $metadata = '';
    $event                  = new SummitEntityEvent();
    $event->EntityClassName = $entity->ClassName;
    $event->EntityID        = $entity->ID;
    $event->Type            = 'DELETE';
    $event->OwnerID         = Member::currentUserID();
    $event->SummitID        = $summit_id;
    $event->Metadata        = $metadata;
    $event->write();
});

PublisherSubscriberManager::getInstance()->subscribe(ISummitEntityEvent::AddedToSchedule, function($member_id, $entity){

    $summit_id = $entity->getField("SummitID");
    if(is_null($summit_id) || $summit_id == 0) $summit_id = Summit::ActiveSummitID();
    $metadata = '';
    $event                  = new SummitEntityEvent();
    $event->EntityClassName = 'MySchedule';
    $event->EntityID        = $entity->ID;
    $event->Type            = 'INSERT';
    $event->OwnerID         = $member_id;
    $event->SummitID        = $summit_id;
    $event->Metadata        = $metadata;
    $event->write();
});

PublisherSubscriberManager::getInstance()->subscribe(ISummitEntityEvent::RemovedToSchedule, function($member_id, $entity){

    $summit_id = $entity->getField("SummitID");
    if(is_null($summit_id) || $summit_id == 0) $summit_id = Summit::ActiveSummitID();
    $metadata = '';
    $event                  = new SummitEntityEvent();
    $event->EntityClassName = 'MySchedule';
    $event->EntityID        = $entity->ID;
    $event->Type            = 'DELETE';
    $event->OwnerID         = $member_id;
    $event->SummitID        = $summit_id;
    $event->Metadata        = $metadata;
    $event->write();
});

PublisherSubscriberManager::getInstance()->subscribe('manymanylist_added_item', function($list, $item){
    if($item instanceof ISummitEvent && $list->getJoinTable() === 'SummitAttendee_Schedule') {
        $summit_id = $item->getField("SummitID");
        if (is_null($summit_id) || $summit_id == 0) {
            $summit_id = Summit::ActiveSummitID();
        }

        $attendee_id = $list->getForeignID();
        $attendee = SummitAttendee::get()->byID($attendee_id);

        $metadata = '';
        $event = new SummitEntityEvent();
        $event->EntityClassName = 'MySchedule';
        $event->EntityID = $item->ID;
        $event->Type = 'INSERT';
        $event->OwnerID = $attendee->MemberID;
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
        $event->Metadata = json_encode( array('presentation_id' => $presentation_id ));
        $event->write();
    }
    if($item instanceof SummitType && $list->getJoinTable() === 'SummitEvent_AllowedSummitTypes')
    {
        $event_id     = intval($list->getForeignID());
        $summit_event = SummitEvent::get()->byID($event_id);
        if(is_null($summit_event)) return;
        $summit_type_id  = $item->ID;
        $event = new SummitEntityEvent();
        $event->EntityClassName = 'SummitTypeFromEvent';
        $event->EntityID = $summit_type_id;
        $event->Type     = 'INSERT';
        $event->OwnerID  = Member::currentUserID();
        $event->SummitID = $summit_event->SummitID;
        $event->Metadata = json_encode( array('event_id' => $event_id ));
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
});

PublisherSubscriberManager::getInstance()->subscribe('manymanylist_removed_item', function($list, $item){

    if($item instanceof ISummitEvent && $list->getJoinTable() === 'SummitAttendee_Schedule') {
        $summit_id = $item->getField("SummitID");
        if (is_null($summit_id) || $summit_id == 0) {
            $summit_id = Summit::ActiveSummitID();
        }
        $metadata = '';
        $attendee_id = $list->getForeignID();
        $attendee = SummitAttendee::get()->byID($attendee_id);

        $event = new SummitEntityEvent();
        $event->EntityClassName = 'MySchedule';
        $event->EntityID = $item->ID;
        $event->Type = 'DELETE';
        $event->OwnerID = $attendee->MemberID;
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
        $event->Metadata = json_encode( array('presentation_id' => $presentation_id ));
        $event->write();
    }
    if($item instanceof SummitType && $list->getJoinTable() === 'SummitEvent_AllowedSummitTypes')
    {
        // removed summit type from event
        $event_id     = intval($list->getForeignID());
        $summit_event = SummitEvent::get()->byID($event_id);
        if(is_null($summit_event)) return;
        $summit_type_id  = $item->ID;
        $event = new SummitEntityEvent();
        $event->EntityClassName = 'SummitTypeFromEvent';
        $event->EntityID = $summit_type_id;
        $event->Type     = 'DELETE';
        $event->OwnerID  = Member::currentUserID();
        $event->SummitID = $summit_event->SummitID;
        $event->Metadata = json_encode( array('event_id' => $event_id ));
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

define('MAX_SUMMIT_ALLOWED_PER_USER', 3);