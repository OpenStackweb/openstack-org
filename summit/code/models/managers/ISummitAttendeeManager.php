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
interface ISummitAttendeeManager
{
    /**
     * @param ISummit $summit
     * @param array $attendee_data
     * @return mixed
     */
    public function addAttendee(ISummit $summit, array $attendee_data);

    /**
     * @param ISummit $summit
     * @param array $attendee_data
     * @return mixed
     */
    public function updateAttendee(ISummit $summit, array $attendee_data);

    /**
     * @param ISummit $summit
     * @param $ticket_id
     * @param $member_id
     * @return mixed
     */
    public function reassignTicket(ISummit $summit, $ticket_id, $member_id);

    /**
     * @param ISummit $summit
     * @param $attendee_id
     * @param $data
     * @return mixed
     */
    public function addAttendeeTicket(ISummit $summit, $attendee_id, $data);

    /**
     * @param ISummit $summit
     * @param int $eb_attendee_id
     * @param int $member_id
     * @return ISummitAttendee
     */
    public function matchEventbriteAttendee(ISummit $summit, $eb_attendee_id, $member_id);
}