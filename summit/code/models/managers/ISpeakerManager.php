<?php
/**
 * Copyright 2017 OpenStack Foundation
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

/**
 * Interface ISpeakerManager
 */
interface ISpeakerManager
{
    /**
     * @param Member $member
     * @return void
     */
    public function ensureSpeakerProfile(Member $member);


    /**
     * @param $term
     * @param bool $obscure_email
     * @return array
     */
    public function getSpeakerByTerm($term, $obscure_email = true);

    /**
     * @param ISummit $summit
     * @param array $speaker_data
     * @param IMessageSenderService $speaker_creation_email_sender
     * @return IPresentationSpeaker
     */
    public function createSpeaker(ISummit $summit, array $speaker_data, IMessageSenderService $speaker_creation_email_sender);

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
     * @param ISummit $speaker_id_1
     * @param ISummit $speaker_id_2
     * @param array $data
     * @return array
     */
    public function mergeSpeakers(ISummit $summit, $speaker_id_1, $speaker_id_2, array $data);

}