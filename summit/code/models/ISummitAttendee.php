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
interface ISummitAttendee extends IEntity
{
    /**
     * @return ICommunityMember
     */
    public function getMember();

    /**
     * @return ISummit
     */
    public function getSummit();

    /**
     * @param ISummitAttendeeTicket $ticket
     * @return bool
     */
    public function hasTicket(ISummitAttendeeTicket $ticket);

    /**
     * @param ISummitAttendeeTicket $ticket
     * @return $this
     */
    public function addTicket(ISummitAttendeeTicket $ticket);

    /**
     * @return void
     */
    public function registerSummitHallChecking();

    /**
     * @return DateTime
     */
    public function getTicketBoughtDate();

    /**
     * @return ISummitAttendeeTicket[]
     * @throws Exception
     */
    public function getTickets();

    /**
     * @return bool
     */
    public function allowSharedContactInfo();

    /**
     * @return bool
     */
    public function isSummitHallCheckedIn();


    /**
     * @param bool $must_share
     * @return $this
     */
    public function setShareContactInfo($must_share);

    /**
     * @return string
     * @throws Exception
     */
    public function getTicketIDs();

    /**
     * @return string
     * @throws Exception
     */
    public function getBoughtDate($format = 'M j Y h:ia');

}