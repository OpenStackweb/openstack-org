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
interface ISummitGeoLocatedLocation extends ISummitLocation
{
    /**
     * @return string
     */
    public function getAddress();

    /**
     * @return string
     */
    public function getCity();

    /**
     * @return string
     */
    public function getZipCode();

    /**
     * @return string
     */
    public function getState();

    /**
     * @return string
     */
    public function getCountry();

    /**
     * @return int
     */
    public function getLng();

    /**
     * @return int
     */
    public function getLat();

    /**
     * @return string
     */
    public function getWebsiteUrl();

    /**
     * @return bool
     */
    public function canDisplayOnSite();

    /**
     * @param IGeoCodingService $geo_service
     * @return void
     */
    public function setCoordinates(IGeoCodingService $geo_service);

    /**
     * @return string[]
     */
    public function getMapsUrls();

    /**
     * @return SummitLocationImage[]
     */
    public function getImages();

    /**
     * @return SummitLocationImage
     */
    public function getFirstImage();
}