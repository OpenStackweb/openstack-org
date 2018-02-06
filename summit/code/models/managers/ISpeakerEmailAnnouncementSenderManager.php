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
interface ISpeakerEmailAnnouncementSenderManager
{
    /**
     * @param ISummit $current_summit
     * @param int $batch_size
     * @return void
     */
    public function sendSpeakersSelectionAnnouncementBySummit(ISummit $current_summit, $batch_size);

    /**
     * @param ISummit $current_summit
     * @param int $batch_size
     * @return void
     */
    public function sendModeratorsSelectionAnnouncementBySummit(ISummit $current_summit, $batch_size);

    /**
     * @param ISummit $current_summit
     * @param IMessageSenderService $sender_service
     * @param int $batch_size
     * @return int
     */
    public function sendUploadSlidesAnnouncementBySummit(ISummit $current_summit, IMessageSenderService $sender_service, $batch_size);

    /**
     * @param IPresentationSpeaker $speaker
     * @param ISummit $current_summit
     * @param bool|string $role
     * @param bool $check_email_existance
     * @return bool
     */
    public function sendSelectionAnnouncementEmailForSpeaker(IPresentationSpeaker $speaker, ISummit $current_summit, $role = IPresentationSpeaker::RoleSpeaker, $check_email_existance = true);

}