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
final class PresentationSpeakerAlternateAnnouncementEmailSender implements IMessageSenderService
{

    /**
     * @param mixed $subject
     * @throws Exception
     */
    public function send($subject)
    {
        if(!$subject instanceof IPresentationSpeaker) return;if(!is_array($subject)) return;
        if(!isset($subject['Summit'])  || !isset($subject['Speaker'])) return;
        $summit  = $subject['Summit'];
        $speaker = $subject['Speaker'];
        if(!$speaker instanceof IPresentationSpeaker) return;
        if(!$summit instanceof ISummit) return;

        $email = PermamailTemplate::get()->filter('Identifier', PRESENTATION_SPEAKER_ALTERNATE_ONLY_EMAIL)->first();
        if(is_null($email)) throw new Exception(sprintf('Email Template %s does not exists on DB!', PRESENTATION_SPEAKER_ALTERNATE_ONLY_EMAIL));

        $speaker->registerAnnouncementEmailTypeSent(IPresentationSpeaker::AnnouncementEmailAlternate, $summit->ID);

        $email = EmailFactory::getInstance()->buildEmail(PRESENTATION_SPEAKER_NOTIFICATION_ACCEPTANCE_EMAIL_FROM, $speaker->getEmail());

        $email->setUserTemplate(PRESENTATION_SPEAKER_ALTERNATE_ONLY_EMAIL)->populateTemplate(
            array
            (
                'Speaker'              => $speaker,
                'ConfirmationLink'     => $speaker->getSpeakerConfirmationLink(),
                'PromoCode'            => $speaker->getSummitPromoCode($summit->ID)->getCode(),
                'Summit'               => $summit,
            )
        )
        ->send();
    }
}