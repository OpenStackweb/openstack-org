<?php
/**
 * Copyright 2018 OpenStack Foundation
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

trait TimeZoneEntity
{
    /**
     * @return string
     */
    public function getTimeZoneIdentifier(){
        return $this->TimeZoneIdentifier;
    }

    /**
     * @param string $time_zone_identifier
     */
    public function setTimeZoneIdentifier($time_zone_identifier){
        $this->TimeZoneIdentifier = $time_zone_identifier;
    }

    public function setDateTimeFromLocalToUTC($value, $field)
    {
        if (!empty($value)) {
            $value = $this->convertDateFromTimeZone2UTC($value);
            $this->setField($field, $value);
        }
    }

    public function getFromUTCtoLocal($field, $format = "Y-m-d H:i:s")
    {
        return $this->convertDateFromUTC2TimeZone($this->getField($field), $format);
    }

    /**
     * @return DateTimeZone|null
     */
    public function getEntityTimeZone()
    {
        // @see http://php.net/manual/en/timezones.php
        $time_zone_identifier = $this->getTimeZoneIdentifier();
        if (empty($time_zone_identifier)) {
            return null;
        }
        try {
            return new DateTimeZone($time_zone_identifier);
        } catch (Exception $ex) {
            return null;
        }
    }

    /**
     * @param $value
     * @param $format
     * @return null|string
     */
    public function convertDateFromTimeZone2UTC($value, $format = "Y-m-d H:i:s")
    {
        $time_zone_identifier = $this->getTimeZoneIdentifier();
        if (empty($time_zone_identifier)) {
            return $value;
        }

        if (empty($value)) {
            return null;
        }

        try {
            $local_time_zone = new DateTimeZone($time_zone_identifier);
        } catch (Exception $ex) {
            return null;
        }

        $utc_timezone = new DateTimeZone("UTC");
        $date = new DateTime($value, $local_time_zone);
        $date->setTimezone($utc_timezone);
        return $date->format($format);
    }

    /**
     * @param $value
     * @param $format
     * @return null|string
     */
    public function convertDateFromUTC2TimeZone($value, $format = "Y-m-d H:i:s")
    {
        $time_zone_identifier = $this->getTimeZoneIdentifier();
        if (empty($time_zone_identifier)) {
            return $value;
        }

        if (empty($value)) {
            return null;
        }

        try {
            $local_time_zone = new DateTimeZone($time_zone_identifier);
        } catch (Exception $ex) {
            return null;
        }

        $utc_timezone = new DateTimeZone("UTC");
        $date = new DateTime($value, $utc_timezone);
        $date->setTimezone($local_time_zone);

        return $date->format($format);

    }

    /**
     * @return string
     */
    public function getTimeZoneName()
    {
        $time_zone_identifier = $this->getTimeZoneIdentifier();
        if (empty($time_zone_identifier)) {
            return 'Not Set';
        }
        return $time_zone_identifier;
    }

    public function getTimeZoneOffsetFriendly()
    {
        $time_zone_identifier = $this->getTimeZoneIdentifier();
        if (empty($time_zone_identifier)) {
            return 'Not Set';
        }
        try {
            $local_time_zone = new DateTimeZone($time_zone_identifier);
            $now = new DateTime("now", $local_time_zone);
            $offset = $local_time_zone->getOffset($now) / 3600;
            return "GMT" . ($offset < 0 ? $offset : "+" . $offset);
        } catch (Exception $ex) {
            return null;
        }
    }

    /**
     * @return array
     */
    public function getTimezones(){
        $timezones_list = [];
        foreach(DateTimeZone::listIdentifiers() as $timezone_identifier){
            $timezones_list[$timezone_identifier] = $timezone_identifier;
        }
        return $timezones_list;
    }
}