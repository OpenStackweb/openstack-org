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
class SpeakerSummitRegistrationPromoCode extends SummitRegistrationPromoCode implements ISpeakerSummitRegistrationPromoCode
{
    private static $db = array
    (
        'Type' => "Enum('ACCEPTED, ALTERNATE','ACCEPTED')",
    );

    private static $has_one = array
    (
        'Speaker' => 'PresentationSpeaker',
    );

    /**
     * @return string
     */
    public function type()
    {
        // TODO: Implement type() method.
    }

    /**
     * @return IPresentationSpeaker
     */
    public function speaker()
    {
        // TODO: Implement speaker() method.
    }
}