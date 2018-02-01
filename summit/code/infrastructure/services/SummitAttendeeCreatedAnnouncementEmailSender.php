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
final class SummitAttendeeCreatedAnnouncementEmailSender implements IMessageSenderService
{

    /**
     * @param $subject
     * @throws InvalidArgumentException
     * @return void
     */
    public function send($subject)
    {
        if(!is_array($subject)) return;
        if(!isset($subject['Summit'])  || !isset($subject['Attendee'])) return;

        $summit   = $subject['Summit'];
        $attendee = $subject['Attendee'];

        $email_template = PermamailTemplate::get_by_identifier(SUMMIT_ATTENDEE_CREATED_EMAIL_TEMPLATE);
        if(is_null($email_template)) return;

        $email = EmailFactory::getInstance()->buildEmail(null, $attendee->getMember()->getEmail());

        $email->setUserTemplate('summit-attendee-created')->populateTemplate(
            array
            (
                'Attendee' => $attendee,
                'Summit'   => $summit
            )
        )
        ->send();
    }
}