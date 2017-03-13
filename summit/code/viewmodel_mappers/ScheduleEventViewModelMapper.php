<?php

/**
 * Copyright 2016 OpenStack Foundation
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
final class ScheduleEventViewModelMapper implements IViewModelMapper
{
    /**
     * @param array $params
     * @return mixed
     */
    public function map(array $params)
    {
        $schedule       = $params[0];
        $summit         = $params[1];
        $events         = [];
        $current_member = Member::currentUser();
        $is_attendee    = is_null($current_member) ? false : $current_member->isAttendee($summit->ID);
        foreach ($schedule as $e) {

            $entry = [
                'id'                 => intval($e->ID),
                'title'              => trim($e->Title),
                'class_name'         => $e->ClassName,
                'abstract'           => $e->Abstract,
                'start_epoch'        => strtotime($e->StartDate),
                'start_datetime'     => $e->StartDate,
                'end_datetime'       => $e->EndDate,
                'start_time'         => $e->StartTime,
                'end_time'           => $e->EndTime,
                'date_nice'          => date('D j', strtotime($e->StartDate)),
                'allow_feedback'     => $e->AllowFeedBack,
                'location_id'        => intval($e->LocationID),
                'type_id'            => intval($e->TypeID),
                'rsvp_link'          => $e->getRSVPURL(),
                'has_rsvp'           => $e->hasRSVP(),
                'rsvp_external'      => $e->isExternalRSVP(),
                'rsvp_seat_type'     => $e->getCurrentRSVPSubmissionSeatType(),
                'sponsors_id'        => [],
                'category_group_ids' => [],
                'tags_id'            => [],
                'going'                    => is_null($current_member) || !$is_attendee ? false : $current_member->isOnMySchedule($e->ID),
                'favorite'                 => is_null($current_member)? false : $current_member->isOnFavorites($e->ID),
                'gcal_id'                  => is_null($current_member) || !$is_attendee ? false : $current_member->getGoogleCalEventId($e->ID),
                'show'                     => true,
                'headcount'                => intval($e->HeadCount),
                'summit_id'                => intval($summit->ID),
                'time_zone_id'             => $summit->getTimeZoneName(),
                'attendees_schedule_count' => $e->AttendeesScheduleCount(),
                'track_id'                 => intval($e->CategoryID),
                'attachment_url'           => '',
                'to_record'                => false,
                'url'                      => $e->getLink('show'),
            ];

            foreach ($e->Tags() as $t) {
                array_push($entry['tags_id'], intval($t->ID));
            }

            $entry['category_group_ids']  = [];
            if ($e->Category()) {
                foreach ($e->Category()->getCategoryGroups() as $group)
                    $entry['category_group_ids'][] = $group->ID;

            }

            foreach ($e->Sponsors() as $e) {
                array_push($entry['sponsors_id'], intval($e->ID));
            }

            if ($e instanceof Presentation) {
                $speakers = array();
                foreach ($e->Speakers() as $s) {
                    array_push($speakers, intval($s->ID));
                }

                $entry['speakers_id']  = $speakers;
                $entry['moderator_id'] = intval($e->ModeratorID);
                $entry['level']        = $e->Level;
                $entry['status']       = $e->SelectionStatus();
                $entry['to_record']    = boolval($e->ToRecord);
            }

            if ($e instanceof SummitEventWithFile) {
                $entry['attachment_url'] = ($e->Attachment()) ? $e->Attachment()->getUrl() : '';
            }

            $events[] = $entry;
        };

        return $events;
    }
}