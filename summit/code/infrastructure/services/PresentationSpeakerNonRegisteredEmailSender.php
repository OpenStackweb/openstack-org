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
 * Class PresentationSpeakerNonRegisteredEmailSender
 * Send a new registration request for non registered Speakers (MemberID === 0)
 */
final class PresentationSpeakerNonRegisteredEmailSender implements IMessageSenderService
{

    /**
     * @param mixed $subject
     * @throws EntityValidationException
     * @throws NotFoundEntityException
     */
    public function send($subject)
    {
        if(!is_array($subject)) return;
        if(!isset($subject['Summit'])  || !isset($subject['Speaker'])) return;

        $summit     = $subject['Summit'];
        $speaker    = $subject['Speaker'];

        if(!$speaker instanceof IPresentationSpeaker) return;
        if(!$summit instanceof ISummit) return;
        if(!$speaker->hasPendingRegistrationRequest()) throw new EntityValidationException('speaker not has a pending registration request!');

        $email = PermamailTemplate::get()->filter('Identifier', PRESENTATION_SPEAKER_CREATE_MEMBERSHIP_EMAIL)->first();
        if(is_null($email)) throw new NotFoundEntityException(sprintf('Email Template %s does not exists on DB!', PRESENTATION_SPEAKER_CREATE_MEMBERSHIP_EMAIL));
        $schedule_page = SummitAppSchedPage::get()->filter('SummitID', $summit->getIdentifier())->first();
        if(is_null($schedule_page)) throw new NotFoundEntityException('Summit Schedule page does not exists!');

        // reset token ...
        $registration_request = $speaker->RegistrationRequest();
        $token                = $registration_request->generateConfirmationToken();
        $registration_request->write();

        $registration_url = Controller::join_links(Director::baseURL(), 'summit-login', 'registration');
        $registration_url = HTTP::setGetVar(SpeakerRegistrationRequest::ConfirmationTokenParamName , $token, $registration_url);

        $speaker->registerCreateMembershipSent();

        $email = EmailFactory::getInstance()->buildEmail(PRESENTATION_SPEAKER_CREATE_MEMBERSHIP_EMAIL, $speaker->getEmail());

        $email->setUserTemplate(PRESENTATION_SPEAKER_CREATE_MEMBERSHIP_EMAIL)->populateTemplate(
            array
            (
                'Speaker'         => $speaker,
                'Summit'          => $summit,
                'RegistrationUrl' => $registration_url
            )
        )
        ->send();

    }
}