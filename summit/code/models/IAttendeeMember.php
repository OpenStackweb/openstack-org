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
interface IAttendeeMember extends IEntity
{
    /**
     * @param int|null $summit_id
     * @return bool
     */
    public function isAttendee($summit_id = null);

    /**
     * @param int $summit_id
     * @return ISummitAttendee
     */
    public function getSummitAttendee($summit_id);

    /**
     * @return ISummitAttendee|null
     */
    public function getCurrentSummitAttendee();

    /**
     * @return ISummitAttendee|null
     */
    public function getUpcomingSummitAttendee();
}