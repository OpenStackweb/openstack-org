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
interface IEventbriteEventManager
{
    /**
     * @param string $type
     * @param string $api_url
     * @return IEventbriteEvent
     */
    public function registerEvent($type, $api_url);

    /**
     * @param int $bach_size
     * @param IMessageSenderService $invite_sender
     * @param IMessageSenderService $create_sender
     * @return mixed
     */
    public function ingestEvents($bach_size, IMessageSenderService $invite_sender, IMessageSenderService $create_sender);


    /**
     * @param $member
     * @param string $external_summit_id
     * @param string $external_order_id
     * @param string $external_attendee_id
     * @param string $external_ticket_class_id
     * @param string $bought_date
     * @param bool $shared_contact_info
     * @return ISummitAttendee
     */
    public function registerAttendee
    (
        $member,
        $external_summit_id,
        $external_order_id,
        $external_attendee_id,
        $external_ticket_class_id,
        $bought_date,
        $shared_contact_info = false
    );

    /**
     * @param $order_external_id
     * @throw InvalidEventbriteOrderStatusException
     */
    public function getOrderAttendees($order_external_id);
}