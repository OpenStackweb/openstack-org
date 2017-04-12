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
class SapphireSummitAttendeeTicketRepository
    extends SapphireRepository
    implements ISummitAttendeeTicketRepository
{

    public function __construct()
    {
        parent::__construct(new SummitAttendeeTicket());
    }

    /**
     * @param $external_order_id
     * @param $external_attendee_id
     * @return ISummitAttendeeTicket
     */
    function getByExternalOrderIdAndExternalAttendeeId($external_order_id, $external_attendee_id)
    {
       return SummitAttendeeTicket::get()->filter([
            'ExternalAttendeeId' => $external_attendee_id,
            'ExternalOrderId'    => $external_order_id,
       ])->first();
    }
}