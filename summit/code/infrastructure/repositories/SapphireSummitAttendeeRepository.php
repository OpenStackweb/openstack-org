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
final class SapphireSummitAttendeeRepository extends SapphireRepository implements ISummitAttendeeRepository
{

    private $cache = array();

    public function __construct()
    {
        parent::__construct(new SummitAttendee);
    }

    /**
     * @param int $member_id
     * @param int $summit_id
     * @return ISummitAttendee
     */
    public function getByMemberAndSummit($member_id, $summit_id)
    {
        $cache_key = sprintf('%s_%s', $member_id, $summit_id);
        $attendee  = SummitAttendee::get()->filter(array
        (
            'MemberID'        => $member_id,
            'SummitID'        => $summit_id,
        ))->first();

        if(is_null($attendee) && isset($this->cache[$cache_key]))
        {
            $attendee = $this->cache[$cache_key];
        }
        return $attendee;
    }

    /**
     * @param int $order_id
     * @param int $attendee_id
     * @return ISummitAttendee
     */
    public function getByOrderAndExternalAttendeeId($order_id, $attendee_id)
    {
        return SummitAttendee::get()->filter(array
        (
            'ExternalId'        => $attendee_id,
            'ExternalOrderId'   => $order_id
        ))->first();
    }

    /**
     * @param IEntity $entity
     * @return int|void
     */
    public function add(IEntity $entity)
    {
        parent::add($entity);
        $this->cache[sprintf('%s_%s', $entity->MemberID, $entity->SummitID)] = $entity;
    }
}