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
interface IScheduleManager
{

    /**
     * @param int $member_id
     * @param int $event_id
     * @return ISummitAttendee
     */
    function addEventToSchedule($member_id, $event_id);

    /**
     * @param int $member_id
     * @param int $event_id
     * @return ISummitAttendee
     */
    function removeEventFromSchedule($member_id, $event_id);

    /**
     * @param array $data
     * @param ISummitEventFeedback $feedback
     * @return ISummitEventFeedback
     */
    public function updateFeedback(array $data, ISummitEventFeedback $feedback);

    /**
     * @param array $data
     * @return ISummitEventFeedBack
     */
    public function addFeedback(array $data);

    /**
     * @param int $member_id
     * @param int $event_id
     * @param string $target
     * @param int $cal_event_id
     * @return int
     */
    public function saveSynchId($member_id, $event_id, $target = 'google' , $cal_event_id);

    /**
     * @param array $data
     * @param IMessageSenderService $sender_service
     * @return RSVP
     */
    public function addRSVP(array $data, IMessageSenderService $sender_service);

}