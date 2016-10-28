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
interface IEventbriteAttendeeRepository extends IEntityRepository
{

    /**
     * @param int $eventbrite_attendee_id
     * @return EventbriteAttendee
     */
    public function getByAttendeeId($eventbrite_attendee_id);

    /**
     * @param string $email
     * @return EventbriteAttendee[]
     */
    public function getByEmail($email);

    /**
     * @param string $search_term
     * @param bool $suggested_only
     * @param int $page
     * @param int $size
     * @return array
     */
    public function getUnmatchedPaged($search_term = '', $suggested_only = false, $page = 1, $size = 20);

    /**
     * @param EventbriteAttendee $eventbrite_attendee
     * @return array
     */
    public function getSuggestions($eventbrite_attendee);
}