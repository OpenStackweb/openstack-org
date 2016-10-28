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
     * @param int $eventbrite_attendee_id
     * @return EventbriteAttendee
     */
    public function getByAttendeeId($eventbrite_attendee_id)
    {
        $query = new QueryObject(new EventbriteAttendee);
        $query->addAndCondition(QueryCriteria::equal('ExternalAttendeeId', $eventbrite_attendee_id));
        return $this->getBy($query);
    }

    /**
     * @param string $email
     * @return EventbriteAttendee[]
     */
    public function getByEmail($email)
    {
        $query = new QueryObject(new EventbriteAttendee);
        $query->addAndCondition(QueryCriteria::equal('Email', $email));
        return $this->getAll($query);
    }

    /**
     * @param string $search_term
     * @param bool $suggested_only
     * @param int $page
     * @param int $size
     * @return array
     */
    public function getUnmatchedPaged($search_term = '', $suggested_only = false, $page = 1, $size = 20)
    {
        $attendees = new ArrayList();
        $offset = $page * $size;

        $query = <<<SQL
        SELECT EBA.*, GROUP_CONCAT(EBA.ExternalAttendeeId SEPARATOR ', ') AS ExternalIds FROM EventBriteAttendee EBA
        WHERE NOT EXISTS
        (SELECT SAT.ID FROM SummitAttendeeTicket SAT WHERE SAT.ExternalAttendeeId = EBA.ExternalAttendeeId)
SQL;
        if ($search_term) {
            $query .= <<<SQL
        AND (EBA.FirstName LIKE '{$search_term}%' OR EBA.LastName LIKE '{$search_term}%' OR EBA.Email LIKE '{$search_term}%')
SQL;
        }

        if ($suggested_only) {
            $query .= <<<SQL
        AND EXISTS
        (SELECT M.ID, M.FirstName, M.Surname, M.Email, M.SecondEmail, M.ThirdEmail
         FROM Member M WHERE M.FirstName = EBA.FirstName AND M.Surname = EBA.LastName)
SQL;
        }

        $query .= <<<SQL
        GROUP BY Email
SQL;
        $total = DB::query($query)->numRecords();

        $query .= <<<SQL
        LIMIT {$page},{$offset};
SQL;

        foreach(DB::query($query) as $row)
        {
            $attendees->push(new ArrayData($row));
        }

        return array($attendees,$total);
    }

    /**
     * @param EventbriteAttendee $eventbrite_attendee
     * @return array
     */
    public function getSuggestions($eventbrite_attendee)
    {
        $suggestions = new ArrayList();

        $query = <<<SQL
SELECT M.ID, M.FirstName, M.Surname, M.Email, 'Name' AS Reason FROM Member M
WHERE M.FirstName LIKE '{$eventbrite_attendee->FirstName}' AND M.Surname LIKE '{$eventbrite_attendee->LastName}'
SQL;
        foreach(DB::query($query) as $row)
        {
            $suggestions->push(new ArrayData($row));
        }

        return $suggestions;
    }
}