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
final class SapphireEventbriteAttendeeRepository extends SapphireRepository  implements IEventbriteAttendeeRepository
{

    public function __construct()
    {
        parent::__construct(new EventbriteAttendee);
    }

    /**
     * @param int $page
     * @param int $size
     * @return array
     */
    public function getUnmatchedPaged($page, $size)
    {
        $attendees = new ArrayList();
        $offset = $page * $size;

        $query = <<<SQL
        SELECT * FROM EventBriteAttendee EBA
        WHERE NOT EXISTS
        (SELECT SAT.ID FROM SummitAttendeeTicket SAT WHERE SAT.ExternalAttendeeId = EBA.ExternalAttendeeId)
        LIMIT {$page},{$offset};
SQL;
        foreach(DB::query($query) as $row)
        {
            $attendees->push(new ArrayData($row));
        }

        return $attendees;
    }

    /**
     * @return array
     */
    public function getUnmatchedCount()
    {
        $query = <<<SQL
        SELECT * FROM EventBriteAttendee EBA
        WHERE NOT EXISTS
        (SELECT SAT.ID FROM SummitAttendeeTicket SAT WHERE SAT.ExternalAttendeeId = EBA.ExternalAttendeeId);
SQL;

        return DB::query($query)->numRecords();
    }

    /**
     * @param int $eventbrite_attendee_id
     * @return array
     */
    public function getSuggestions($eventbrite_attendee_id)
    {

    }
}