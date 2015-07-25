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
interface ISummit extends IEntity
{
    /**
     * @return string
     */
    public function getTitle();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return DateTime
     */
    public function getBeginDate();

    /**
     * @return DateTime
     */
    public function getEndDate();

    /**
     * @return DateTime
     */
    public function getSubmissionBeginDate();

    /**
     * @return DateTime
     */
    public function getSubmissionEndDate();

    /**
     * @return DateTime
     */
    public function getVotingBeginDate();

    /**
     * @return DateTime
     */
    public function getVotingEndDate();

    /**
     * @return DateTime
     */
    public function getSelectionBeginDate();

    /**
     * @return DateTime
     */
    public function getSelectionEndDate();

    /**
     * @return ISummitType[]
     */
    public function getTypes();

    /**
     * @param ISummitType $type
     * @return void
     */
    public function addType(ISummitType $type);

    /**
     * @return void
     */
    public function clearAllTypes();


    /**
     * @return ISummitAirport[]
     */
    public function getAirports();

    /**
     * @param ISummitAirport $airport
     * @return void
     */
    public function addAirport(ISummitAirport $airport);

    /**
     * @return void
     */
    public function clearAllAirports();

    /**
     * @param bool|false $show_all
     * @param string $hotel_type
     * @return ISummitHotel[]
     */
    public function getHotels($show_all = false, $hotel_type = 'Primary');

    /**
     * @param ISummitHotel $hotel
     * @return void
     */
    public function addHotel(ISummitHotel $hotel);

    /**
     * @return void
     */
    public function clearAllHotels();

    /**
     * @return ISummitVenue[]
     */
    public function getVenues();

    /**
     * @param ISummitVenue $venue
     * @return void
     */
    public function addVenue(ISummitVenue $venue);

    /**
     * @return void
     */
    public function clearAllVenues();

    /**
     * @return string
     */
    public function getStatus();

    /**
     * @return bool
     */
    public function IsCurrent();

    /**
     * @return bool
     */
    public function IsUpComing();

    /**
     * @return ISummit
     */
    public static function CurrentSummit();

    /**
     * @param ISummitEvent $summit_event
     * @return bool
     */
    public function isEventInsideSummitDuration(ISummitEvent $summit_event);


    /**
     * @param string $ticket_external_id
     * @return ISummitTicketType
     */
    public function findTicketTypeByExternalId($ticket_external_id);

}