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
        if(!isset($subject['Event']) || !isset($subject['Member']) || !isset($subject['Rsvp'])) return;
        $event     = $subject['Event'];
        $member    = $subject['Member'];
        $rsvp      = $subject['Rsvp'];

        //create confirmation number
        $summit_title = substr($event->Summit()->Title,0,3);
        $summit_year = date('y', strtotime($event->Summit()->SummitBeginDate));
        $confirmation_nbr = strtoupper($summit_title).$summit_year.$rsvp->ID;
        $emailTemplate = ($rsvp->SeatType == IRSVP::SeatTypeRegular) ? SUMMIT_ATTENDEE_RSVP_EMAIL : SUMMIT_ATTENDEE_RSVP_WAITLIST_EMAIL;

        $email = PermamailTemplate::get()->filter('Identifier', $emailTemplate)->first();
        if(is_null($email)) throw new Exception(sprintf('Email Template %s does not exists on DB!', $emailTemplate));

        $email = EmailFactory::getInstance()->buildEmail(SUMMIT_ATTENDEE_RSVP_EMAIL_FROM, $member->getEmail());

        $schedule_page = SummitAppSchedPage::getBy($event->Summit());
        if(is_null($schedule_page)) throw new NotFoundEntityException('Summit Schedule page does not exists!');

        $email->setUserTemplate($emailTemplate)->populateTemplate(
            array
            (
                'Event'                => $event,
                'Member'               => $member,
                'ScheduleURL'          => $schedule_page->getAbsoluteLiveLink(false),
                'ConfirmationNbr'      => $confirmation_nbr
            )
        )
        ->send();
    }
}