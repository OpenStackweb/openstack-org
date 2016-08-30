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
     * @return ISummitEvent[]
     */
    public function getSchedule();

    /**
     * @param ISummitEvent $summit_event
     * @return void
     */
    public function addToSchedule(ISummitEvent $summit_event);

    /**
     * @param int $event_id
     * @return bool
     */
    public function isScheduled($event_id);

    /**
     * @return void
     */
    public function clearSchedule();

    public function registerCheckInOnEvent(ISummitEvent $summit_event);

    /**
     * @param bool $must_share
     * @return $this
     */
    public function setShareContactInfo($must_share);

    /**
     * @param ISummitEvent $event
     * @return string
     */
    public function getGoogleCalEventId(ISummitEvent $event);

    /**
     * @param ISummitEvent $event
     * @param int $google_event_id
     * @return string
     */
    public function setGoogleCalEventId(ISummitEvent $event, $google_event_id);

    /*
     * @param int $event_id
     * @return bool
     */
    public function hasRSVPSubmission($event_id);

}