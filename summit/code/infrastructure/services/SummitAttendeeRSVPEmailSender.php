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
final class SummitAttendeeRSVPEmailSender implements IMessageSenderService
{

    /**
     * @param mixed $subject
     * @throws Exception
     */
    public function send($subject)
    {

        if(!is_array($subject)) return;
        if(!isset($subject['Event']) || !isset($subject['Attendee']) || !isset($subject['RsvpID'])) return;
        $event     = $subject['Event'];
        $attendee  = $subject['Attendee'];
        $rsvp_id   = $subject['RsvpID'];

        if(!$attendee instanceof  ISummitAttendee) return;

        //create confirmation number
        $summit_title = substr($event->Summit()->Title,0,3);
        $summit_year = date('y', strtotime($event->Summit()->SummitBeginDate));
        $confirmation_nbr = strtoupper($summit_title).$summit_year.$rsvp_id;

        $email = PermamailTemplate::get()->filter('Identifier', SUMMIT_ATTENDEE_RSVP_EMAIL)->first();
        if(is_null($email)) throw new Exception(sprintf('Email Template %s does not exists on DB!', SUMMIT_ATTENDEE_RSVP_EMAIL));

        $email = EmailFactory::getInstance()->buildEmail(SUMMIT_ATTENDEE_RSVP_EMAIL_FROM, $attendee->getMember()->getEmail());

        $schedule_page = SummitAppSchedPage::getBy($event->Summit());
        if(is_null($schedule_page)) throw new NotFoundEntityException('Summit Schedule page does not exists!');

        $email->setUserTemplate(SUMMIT_ATTENDEE_RSVP_EMAIL)->populateTemplate(
            array
            (
                'Event'                => $event,
                'Attendee'             => $attendee,
                'ScheduleURL'          => $schedule_page->getAbsoluteLiveLink(false),
                'ConfirmationNbr'      => $confirmation_nbr
            )
        )
        ->send();
    }
}