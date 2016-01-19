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
interface ISummitAttendeeRepository extends IEntityRepository
{
    /**
     * @param int $member_id
     * @param int $summit_id
     * @param int $order_id
     * @return ISummitAttendee
     */
    public function getByMemberAndSummitAndOrder($member_id, $summit_id, $order_id);

    /**
     * @param $order_id
     * @param $attendee_id
     * @return ISummitAttendee
     */
    public function getByOrderAndExternalAttendeeId($order_id, $attendee_id);
}