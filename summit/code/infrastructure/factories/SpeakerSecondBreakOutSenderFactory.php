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
final class SpeakerSecondBreakOutSenderFactory implements ISpeakerSecondBreakOutSenderFactory
{

    /**
     * @param ISummit $summit
     * @param IPresentationSpeaker $speaker
     * @return IMessageSenderService
     */
    public function build(ISummit $summit, IPresentationSpeaker $speaker)
    {
        /**
         * Rules are:
         * All speakers that are in a Track, besides BoF and Working Groups
         * Send the code they already received, unless they are new and don’t have a code. Then they get a new one.
         * Send the custom registration link to say they’re coming to the summit and leave their onsite phone
         * ( if they are registered, that is memberid <> 0)
         * If the user is already registered, we shouldn't send their code again: ( confirmed assistance for summit)
         * they still need the email, just not the part with the code. Probably a slightly altered verbiage as well
         */
        if($speaker->breakoutEmailAlreadySent($summit->getIdentifier())) return null;

        if($speaker->hasConfirmedAssistanceFor($summit->getIdentifier())){
            // send reminder without code
            return new PresentationSpeakerSummitReminderEmailSender;
        }

        // send reminder with code
        return new PresentationSpeakerConfirmSummitAssistanceEmailReminderSender();
    }
}