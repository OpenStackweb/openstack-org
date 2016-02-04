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
class MemberRegistrationSenderService implements IMessageSenderService
{

    /**
     * @param mixed $subject
     * @throws InvalidArgumentException
     * @return void
     */
    public function send($subject)
    {
        if(!$subject instanceof Member) return;

        $email_template = PermamailTemplate::get_by_identifier(MEMBER_REGISTRATION_VERIFICATION_EMAIL_TEMPLATE_ID);
        if (is_null($email_template)) {
            return;
        }

        $from  = null;
        $email = EmailFactory::getInstance()->buildEmail($from, $subject->Email);

        $token = $subject->generateEmailVerificationToken();



        $email->setUserTemplate(MEMBER_REGISTRATION_VERIFICATION_EMAIL_TEMPLATE_ID)->populateTemplate(
            array
            (
                'Member'           => $subject,
                'VerificationLink' => sprintf("%smembers/verification/%s", Director::absoluteBaseURL(), $token),
            )
        )->send();
    }
}