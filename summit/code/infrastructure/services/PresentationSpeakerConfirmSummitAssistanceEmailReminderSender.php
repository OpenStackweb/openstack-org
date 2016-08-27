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

/**
 * Class PresentationSpeakerConfirmSummitAssistanceEmailReminderSender
 * Send reminder with code
 */
final class PresentationSpeakerConfirmSummitAssistanceEmailReminderSender implements IMessageSenderService
{
    /**
     * @param mixed $subject
     * @throws NotFoundEntityException
     * @throws EntityValidationException
     */
    public function send($subject)
    {
        if(!is_array($subject)) return;
        if(!isset($subject['Summit'])  || !isset($subject['Speaker'])) return;
        $summit     = $subject['Summit'];
        $speaker    = $subject['Speaker'];
        $promo_code = $subject['PromoCode'];

        if(!$speaker instanceof IPresentationSpeaker) return;
        if(!$summit instanceof ISummit) return;
        if(!$promo_code instanceof SpeakerSummitRegistrationPromoCode) return;

        if($speaker->hasConfirmedAssistanceFor($summit->getIdentifier())) throw new EntityValidationException('Speaker Already confirmed Assistance!');
        if($speaker->breakoutEmailAlreadySent($summit->getIdentifier()))  throw new EntityValidationException('Speaker Email already Sent!');
        $email = PermamailTemplate::get()->filter('Identifier', PRESENTATION_SPEAKER_CONFIRM_SUMMIT_ASSISTANCE_EMAIL)->first();
        if(is_null($email)) throw new NotFoundEntityException(sprintf('Email Template %s does not exists on DB!', PRESENTATION_SPEAKER_CONFIRM_SUMMIT_ASSISTANCE_EMAIL));

        $schedule_page = SummitAppSchedPage::get()->filter('SummitID', $summit->getIdentifier())->first();
        if(is_null($schedule_page)) throw new NotFoundEntityException('Summit Schedule page does not exists!');

        $confirmation_link = $speaker->hasAssistanceFor($summit->getIdentifier())?
            $speaker->resetConfirmationLink($summit->getIdentifier()):
            $speaker->getSpeakerConfirmationLink($summit->getIdentifier());

        $speaker->registerBreakOutSent($summit->getIdentifier(), 'SECOND_BREAKOUT_REGISTER');

        $email = EmailFactory::getInstance()->buildEmail(null, $speaker->getEmail());

        $email->setUserTemplate(PRESENTATION_SPEAKER_CONFIRM_SUMMIT_ASSISTANCE_EMAIL)->populateTemplate(
            array
            (
                'Speaker'              => $speaker,
                'ConfirmationLink'     => $confirmation_link,
                'PromoCode'            => $promo_code->getCode(),
                'Summit'               => $summit,
                'ScheduleMainPageLink' => $schedule_page->getAbsoluteLiveLink(false),
            )
        )->send();
    }
}