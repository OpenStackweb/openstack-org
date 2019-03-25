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
     * @param ISummitEvent $summit_event
     * @return void
     */
    public function removeFromSchedule(ISummitEvent $summit_event);

    /**
     * @param int $event_id
     * @return bool
     */
    public function isOnMySchedule($event_id);

    /**
     * @param int $summit_id
     * @return int[]
     */
    public function getScheduleEventIds($summit_id);

    /**
     * @param int $summit_id
     * @param array $sort
     * @return DataList
     */
    public function getScheduleBySummit($summit_id, array $sort = []);

    /**
     * @return void
     */
    public function clearSchedule();

    /**
     * @return DataList
     */
    public function getAllowedSchedule();

    /*
     * @param int $event_id
     * @return bool
     */
    public function hasRSVPSubmission($event_id);

    /**
     * @param int $summit_id
     * @return int[]
     */
    public function getFavoritesEventIds($summit_id);

    /**
     * @param SummitEvent $event
     */
    public function removeFromFavorites(SummitEvent $event);

    /**
     * @param SummitEvent $event
     */
    public function addToFavorites(SummitEvent $event);

    /**
     * @param int $event_id
     * @return bool
     */
    public function isOnFavorites($event_id);

    /**
     * @param int $summit_id
     * @param array $sort
     * @return DataList
     */
    public function getFavoritesBySummit($summit_id, array $sort = []);

    /**
     * @param Summit $summit
     * @param string $access_token
     * @param string $refresh_token
     * @return CalendarSyncInfo
     * @throws EntityValidationException
     */
    public function registerGoogleAuthGrant(Summit $summit, $access_token, $refresh_token);

    /**
     * @param Summit $summit
     * @param string $access_token
     * @param string $refresh_token
     * @return CalendarSyncInfo
     * @throws EntityValidationException
     */
    public function registerOutlookAuthGrant(Summit $summit, $access_token, $refresh_token);

    /**
     * @param Summit $summit
     * @param string $user
     * @param string $password
     * @param string $user_ppal_url
     * @return CalendarSyncInfo
     * @throws EntityValidationException
     */
    public function registerICloudAuthGrant(Summit $summit, $user, $password, $user_ppal_url);

    /**
     * @param string $provider
     * @param int $summit_id
     * @return bool
     */
    public function existCalendarSyncInfoForProviderAndSummit($provider, $summit_id);

    /**
     * @param int $summit_id
     * @return CalendarSyncInfo|null
     */
    public function getCalendarSyncInfoBy($summit_id);

    /**
     * @param Summit $summit
     * @return PersonalCalendarShareInfo
     * @throws EntityValidationException
     */
    public function createCalendarShareableLink(Summit $summit);

    /**
     * @param string $provider
     * @param int $summit_id
     * @return bool
     */
    public function existCalendarShareableLinkForSummit(int $summit_id):bool;

    /**
     * @param int $summit_id
     * @return mixed
     */
    public function getCalendarShareableLinkForSummit(int $summit_id);

}