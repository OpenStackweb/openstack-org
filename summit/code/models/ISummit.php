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
     * @return string
     */
    public function getScheduleLink();

    /**
     * @return string
     */
    public function getTrackListLink();

    /**
     * @return string
     */
    public function getCallForPresentationsLink();

    /**
     * @return DateTime
     */
    public function getStartShowingVenuesDate();

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
     * @return ISummitVenue[]
     */
    public function getPrimaryVenues();

    /**
     * @return ISummitVenue
     */
    public function getMainVenue();

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

    /**
     * @return bool
     */
    public function isCallForSpeakersOpen();

    /**
     * @return bool
     */
    public function isVotingOpen();

    /**
     * @return ICompany[]
     */
    public function Sponsors();

    /**
     * @param $date
     * @return bool
     */
    public function belongsToDuration($date);

    /**
     * @return bool
     */
    public function ShouldShowVenues();

    /**
     * @return bool
    */
    public function isPresentationEditionAllowed();

    /**
     * @return string
     */
    public function Month();

    /**
     * @return bool
     */
    public function isSelectionOpen();

    /**
     * @return bool
     */
    public function isSelectionOver();

    /**
     * @return bool
     */
    public function isScheduleDisplayed();

    /**
     * @param mixed|null $day
     * @param int|null $location
     * @return SummitEvent[]
     * @throws Exception
     */
    public function getSchedule($day = null, $location = null);

    /**
     * @param mixed|null $level
     * @return SummitEvent[]
     * @throws Exception
     */
    public function getScheduleByLevel($level = null);

    /**
     * @param int|null $track
     * @return SummitEvent[]
     * @throws Exception
     */
    public function getScheduleByTrack($track = null);

    /**
     * @param string $day
     * @return bool
     */
    public function isDayBelongs($day);

    /**
     * @param string $day
     * @param SummitAbstractLocation $location
     * @return int
     */
    public function getPublishedEventsCountByDateLocation($day, SummitAbstractLocation $location);

    /**
     * @param $value
     * @param $format
     * @return null|string
     */
    public function convertDateFromUTC2TimeZone($value, $format);

    /**
     * @param $value
     * @param $format
     * @return null|string
     */
    public function convertDateFromTimeZone2UTC($value, $format);

    /**
     * @return null|string
     */
    public function getTimeZoneName();

    /**
     * @return null|string
     */
    public function getLocalTime($format="Y-m-d H:i:s");

    /**
     * @return PresentationCategory[]
     */
    public function getCategories();

    /**
     * @return PresentationCategoryGroup[]
     */
    public function getPublicCategoryGroups();

    /**
     * @return PrivatePresentationCategoryGroup[]
     */
    public function getPrivateCategoryGroups();

    /**
     * @return PresentationCategory[]
     */
    public function getPublicCategories();

    /**
     * @param PresentationCategory $category
     * @return bool
     */
    public function isPublicCategory(PresentationCategory $category);

    /**
     * @param PresentationCategory $category
     * @return bool
     */
    public function isPrivateCategory(PresentationCategory $category);

    /**
     * @param PresentationCategory $category
     * @return null|PrivatePresentationCategoryGroup
     */
    public function getPrivateGroupFor(PresentationCategory $category);

    /**
     * @return string
     */
    public function getExternalEventId();

}