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
 * Class PresentationModeratorNotificationEmailMessageSender
 */
final class PresentationModeratorNotificationEmailMessageSender implements IMessageSenderService
{
    /**
     * @param mixed $subject
     * @throws InvalidArgumentException
     * @return void
     */
    public function send($subject)
    {
        if (!is_array($subject)) return;
        if (!isset($subject['Presentation'])) return;

        $presentation = $subject['Presentation'];

        // check if template exists
        $email_template = PermamailTemplate::get_by_identifier('presentation-moderator-notification');
        if (is_null($email_template)) {
            return;
        }

        $from = null;
        $subject = null;
        $email = EmailFactory::getInstance()->buildEmail($from, $presentation->Creator()->Email, $subject);

        $email->setUserTemplate('presentation-moderator-notification');

        $email->populateTemplate
        (
            array
            (
                'Moderator' => $presentation->Moderator(),
                'Summit' => $presentation->Summit(),
                'Link' => Director::absoluteBaseURL() . Director::makeRelative($presentation->EditLink()),
                'PasswordLink' => Director::absoluteBaseURL() . '/lostpassword'
            )
        );

        $email->send();
    }
}