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
interface ISummitEvent extends IEntity
{
    /**
     * @return DateTime
     */
    public function getStartDate();

    /**
     * @return DateTime
     */
    public function getEndDate();

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @return ISummitLocation
     */
    public function getLocation();

    /**
     * @param ISummitLocation $location
     * @return void
     */
    public function registerLocation(ISummitLocation $location);

    /**
     * @return ICompany[]
     */
    public function getSponsors();

    /**
     * @param ICompany $company
     * @return void
     */
    public function addSponsor(ICompany $company);

    /**
     * @return void
     */
    public function clearAllSponsors();

    /**
     * @return ISummitEventType
     */
    public function getType();

    /**
     * @param ISummitEventType $type
     * @return void
     */
    public function setType(ISummitEventType $type);

    /**
     * @return ISummitType[]
     */
    public function getAllowedSummitTypes();

    /**
     * @param ISummitType $summit_type
     * @return void
     */
    public function addAllowedSummitType(ISummitType $summit_type);

    /**
     * @return void
     */
    public function clearAllAllowedSummitTypes();

    /**
     * @return ISummit
     */
    public function getSummit();

    /**
     * @return ISummitEventFeedBack[]
     */
    public function getFeedback();

    /**
     * @param ISummitEventFeedBack $feedback
     * @return void
     */
    public function addFeedback(ISummitEventFeedBack $feedback);

    /**
     * @return void
     */
    public function clearAllFeedback();

    /**
     * @return void
     */
    public function publish();

    /**
     * @return bool
     */
    public function isPublished();

    /**
     * @return void
     */
    public function unPublish();
}