<?php
/**
 * Copyright 2018 OpenStack Foundation
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
 * Class CalendarSyncErrorEmailMessageSender
 */
final class CalendarSyncErrorEmailMessageSender implements IMessageSenderService
{

    public function send($subject)
    {
        $sync_info = $subject['CalendarSyncInfo'];
        if (!$sync_info instanceof CalendarSyncInfo) return;

        $email = PermamailTemplate::get()->filter('Identifier', SUMMIT_CALENDAR_SYNC_ERROR_EMAIL_TEMPLATE)->first();
        if (is_null($email)) throw new Exception(sprintf('Email Template %s does not exists on DB!', SUMMIT_CALENDAR_SYNC_ERROR_EMAIL_TEMPLATE));
        $summit = $sync_info->Summit();
        $subject = sprintf(SUMMIT_CALENDAR_SYNC_ERROR_EMAIL_SUBJECT, $sync_info->Provider, $summit->Title);
        $email = EmailFactory::getInstance()->buildEmail(SUMMIT_CALENDAR_SYNC_ERROR_EMAIL_FROM, $sync_info->Owner()->Email);
        $schedule_page = SummitAppSchedPage::getBy($summit);
        if (is_null($schedule_page)) throw new Exception('Summit Schedule page does not exists!');
        $email->setSubject($subject);
        $email->setUserTemplate(SUMMIT_CALENDAR_SYNC_ERROR_EMAIL_TEMPLATE)->populateTemplate(
            [
                'Summit'   => $summit,
                'Member'   => $sync_info->Owner(),
                'Provider' => $sync_info->Provider,
                'CalendarSyncUrl' => $schedule_page->getAbsoluteLiveLink(false) . 'sync-cal'
            ]
        )->send();
    }
}