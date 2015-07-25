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
final class SummitAttendeeFactory implements ISummitAttendeeFactory
{

    /**
     * @param Member $member
     * @param ISummit $summit
     * @param string $external_attendee_id
     * @param string $order_external_id
     * @param ISummitTicketType $ticket_type
     * @param string $bought_date
     * @param bool $shared_contact_info
     * @return ISummitAttendee
     */
    public function build(Member $member, ISummit $summit, $external_attendee_id , $order_external_id, ISummitTicketType $ticket_type , $bought_date, $shared_contact_info = false)
    {
        $attendee = new SummitAttendee;
        $attendee->MemberID = $member->ID;
        $attendee->SummitID = $summit->ID;
        $attendee->ExternalId = $external_attendee_id;
        $attendee->ExternalOrderId = $order_external_id;
        $attendee->TicketTypeID    = $ticket_type->getIdentifier();
        $attendee->ExternalTicketClassID = $ticket_type->getExternalId();
        $attendee->SharedContactInfo = $shared_contact_info;
        $attendee->TicketBoughtDate  = $bought_date;
        return $attendee;
    }
}