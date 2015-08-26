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
final class PresentationSpeakerAcceptedAnnouncementEmailSender implements IMessageSenderService
{

    /**
     * @param IEntity $subject
     * @throws InvalidArgumentException
     * @return void
     */
    public function send(IEntity $subject)
    {
        if(!$subject instanceof IPresentationSpeaker) return;

        $subject->registerAnnouncementEmailTypeSent(IPresentationSpeaker::AnnouncementEmailAccepted);

        $email = EmailFactory::getInstance()->buildEmail('speakersupport@openstack.org', $subject->getEmail());

        $email->setUserTemplate('presentation-speaker-accepted-only')->populateTemplate(
            array
            (
                'Speaker'              => $subject,
                'ConfirmationLink'     => $subject->getSpeakerConfirmationLink(),
                'ScheduleMainPageLink' => Summit::get_active()->SchedUrl,
                'PromoCode'            => $subject->getSummitPromoCode()->getCode(),
            )
        )
        ->send();
    }
}