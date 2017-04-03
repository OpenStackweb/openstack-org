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
final class ScheduleEventSearchViewModelMapper implements IViewModelMapper
{

    /**
     * @param array $params
     * @return mixed
     */
    public function map(array $params)
    {
        $search_results = $params[0];
        $summit         = $params[1];
        $events         = [];
        $current_member = Member::currentUser();
        $is_attendee    = is_null($current_member) ? false : $current_member->isAttendee($summit->ID);

        foreach ($search_results as $e) {
            $entry = array
            (
                'id' => $e->ID,
                'title' => $e->Title,
                'abstract' => $e->Abstract,
                'start_date' => date('Y-m-d', strtotime($e->StartDate)),
                'start_datetime' => $e->StartDate,
                'end_datetime' => $e->EndDate,
                'start_time' => $e->StartTime,
                'end_time' => $e->EndTime,
                'allow_feedback' => $e->AllowFeedBack,
                'location_id' => $e->LocationID,
                'type_id' => $e->TypeID,
                'sponsors_id' => array(),
                'category_group_ids' => array(),
                'tags_id' => array(),
                'own' => is_null($current_member) || !$is_attendee ? false : $current_member->isOnMySchedule($e->ID),
                'favorite' => false,
                'show' => true,
                'attachment_url' => '',
                'to_record' => false,
                'social_summary' => $e->SocialSummary,
            );

            foreach ($e->Tags() as $t) {
                array_push($entry['tags_id'], $t->ID);
            }

            $entry['category_group_ids']  = [];
            if ($e->Category()) {
                foreach ($e->Category()->getCategoryGroups() as $group)
                    $entry['category_group_ids'][] = $group->ID;

            }

            foreach ($e->Sponsors() as $e) {
                array_push($entry['sponsors_id'], $e->ID);
            }

            if ($e instanceof Presentation) {
                $speakers = array();
                foreach ($e->Speakers() as $s) {
                    array_push($speakers, $s->ID);
                }

                $entry['speakers_id'] = $speakers;
                $entry['moderator_id'] = $e->ModeratorID;
                $entry['track_id'] = $e->CategoryID;
                $entry['level'] = $e->Level;
                $entry['to_record'] = boolval($e->ToRecord);
            }

            if ($e instanceof SummitEventWithFile) {
                $entry['attachment_url'] = ($e->Attachment()->Exists()) ? $e->Attachment()->getUrl() : '';
            }

            $events[] = $entry;
        };

        return $events;
    }
}