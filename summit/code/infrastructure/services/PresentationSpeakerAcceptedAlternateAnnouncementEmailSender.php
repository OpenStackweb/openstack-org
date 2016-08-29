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
final class PresentationSpeakerAcceptedAlternateAnnouncementEmailSender implements IMessageSenderService
{

    /**
     * @param mixed $subject
     * @throws Exception
     */
    public function send($subject)
    {
        if(!is_array($subject)) return;
        if(!isset($subject['Summit'])  || !isset($subject['Speaker']) || !isset($subject['PromoCode']) ) return;
        $summit     = $subject['Summit'];
        $speaker    = $subject['Speaker'];
        $promo_code = $subject['PromoCode'];
        $role       = $subject['Role'];
        if(!$speaker instanceof IPresentationSpeaker) return;
        if(!$summit instanceof ISummit) return;
        if(!$promo_code instanceof SpeakerSummitRegistrationPromoCode) return;

        $email = PermamailTemplate::get()->filter('Identifier', PRESENTATION_SPEAKER_ACCEPTED_ALTERNATE_EMAIL)->first();
        if(is_null($email)) throw new Exception(sprintf('Email Template %s does not exists on DB!', PRESENTATION_SPEAKER_ACCEPTED_ALTERNATE_EMAIL));

        $speaker->registerAnnouncementEmailTypeSent(IPresentationSpeaker::AnnouncementEmailAcceptedAlternate, $summit->ID);

        $email = EmailFactory::getInstance()->buildEmail(PRESENTATION_SPEAKER_NOTIFICATION_ACCEPTANCE_EMAIL_FROM, $speaker->getEmail());

        $schedule_page = SummitAppSchedPage::getBy($summit);
        if(is_null($schedule_page)) throw new Exception('Summit Schedule page does not exists!');

        $email->setUserTemplate(PRESENTATION_SPEAKER_ACCEPTED_ALTERNATE_EMAIL)->populateTemplate(
            array
            (
                'Speaker'              => $speaker,
                'Role'                 => $role,
                'ConfirmationLink'     => $speaker->getSpeakerConfirmationLink($summit->ID),
                'PromoCode'            => $promo_code->getCode(),
                'Summit'               => $summit,
                'ScheduleMainPageLink' => $schedule_page->getAbsoluteLiveLink(false),
            )
        )
        ->send();
    }
}