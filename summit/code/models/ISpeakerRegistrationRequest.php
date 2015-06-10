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

/**
 * Interface ISpeakerRegistrationRequest
 */
interface ISpeakerRegistrationRequest
    extends IEntity {


    /**
     * @param string $token
     * @return bool
     * @throws InvalidHashSpeakerRegistrationRequestException
     * @throws SpeakerRegistrationRequestAlreadyConfirmedException
     */
    public function confirm($token);


    /**
     * @return string
     */
    public function proposedSpeakerEmail();

    /**
     * @return string
     */
    public function proposedSpeakerFirstName();

    /**
     * @return string
     */
    public function proposedSpeakerLastName();

    /**
     * @return string
     */
    public function generateConfirmationToken();

    /**
     * @return string
     */
    public function getToken();

    /**
     * @return IPresentationSpeaker
     */
    public function associatedSpeaker();

    /**
     * @return bool
     */
    public function alreadyConfirmed();
}