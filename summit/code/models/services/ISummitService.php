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
interface ISummitService
{
    /**
     * @param ISummit $summit
     * @param array $event_data
     * @return mixed
     */
    public function publishEvent(ISummit $summit, array $event_data);

    /**
     * @param ISummit $summit
     * @param ISummitEvent $event
     * @return mixed
     */
    public function unpublishEvent(ISummit $summit, ISummitEvent $event);

    /**
     * @param ISummit $summit
     * @param array $event_data
     * @return mixed
     */
    public function updateEvent(ISummit $summit, array $event_data);

    /**
     * @param ISummit $summit
     * @param array $event_data
     * @return mixed
     */
    public function createEvent(ISummit $summit, array $event_data);

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
     * @param array $data
     */
    public function updateAndPublishBulkEvents(ISummit $summit, array $data);

    /**
     * @param ISummit $summit
     * @param array $data
     */
    public function updateBulkEvents(ISummit $summit, array $data);


    /**
     * @param ISummit $summit
     * @param array $event_ids
     */
    public function unPublishBulkEvents(ISummit $summit, array $event_ids);

    /**
     * @param ISummit $summit
     * @param array $assistance_data
     * @return mixed
     */
    public function updateAssistance(ISummit $summit, array $assistance_data);

    /**
     * @param ISummit $summit
     * @param $data
     */
    public function updateHeadCount(ISummit $summit, $data);

    /**
     * @param ISummit $summit
     * @param $data
     */
    public function updateVideoDisplay(ISummit $summit, $data);

    /**
     * @param $report_name
     * @param $data
     */
    public function updateReportConfig($report_name, $data);

    /**
     * @param ISummit $summit
     * @param array $speaker_data
     * @return IPresentationSpeaker
     */
    public function createSpeaker(ISummit $summit, array $speaker_data);

    /**
     * @param ISummit $summit
     * @param array $speaker_data
     * @return IPresentationSpeaker
     */
    public function updateSpeaker(ISummit $summit, array $speaker_data);

    /**
     * @param ISummit $summit
     * @param $speaker_id
     * @param $tmp_file
     * @return BetterImage
     */
    public function uploadSpeakerPic(ISummit $summit, $speaker_id, $tmp_file);

    /**
     * @param ISummit $summit
     * @param array $promocode_data
     * @return ISummitRegistrationPromoCode
     */
    public function createPromoCode(ISummit $summit, array $promocode_data);

    /**
     * @param ISummit $summit
     * @param array $promocode_data
     * @return ISummitRegistrationPromoCode
     */
    public function updatePromoCode(ISummit $summit, array $promocode_data);

    /**
     * @param ISummit $summit
     * @param array $data
     * @return ISummitRegistrationPromoCode
     */
    public function setMultiPromoCodes(ISummit $summit, array $data);

    /**
     * @param ISummit $summit
     * @param int $code_id
     * @return ISummitRegistrationPromoCode
     */
    public function sendEmailPromoCode(ISummit $summit, $code_id);


}