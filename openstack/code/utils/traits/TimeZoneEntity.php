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
    public function getEntityTimeZone(){
        $time_zone_id = $this->TimeZone;
        if (empty($time_zone_id)) {
            return null;
        }
        $time_zone_list = timezone_identifiers_list();
        if (isset($time_zone_list[$time_zone_id])) {
            $time_zone_name = $time_zone_list[$time_zone_id];
            return $time_zone = new DateTimeZone($time_zone_name);
        }

        return null;
    }
    /**
     * @param $value
     * @param $format
     * @return null|string
     */
    public function convertDateFromTimeZone2UTC($value, $format = "Y-m-d H:i:s")
    {
        $time_zone_id = $this->TimeZone;
        if (empty($time_zone_id)) {
            return $value;
        }
        $time_zone_list = timezone_identifiers_list();

        if (isset($time_zone_list[$time_zone_id]) && !empty($value)) {
            $utc_timezone = new DateTimeZone("UTC");
            $time_zone_name = $time_zone_list[$time_zone_id];
            $time_zone = new DateTimeZone($time_zone_name);
            $date = new DateTime($value, $time_zone);
            $date->setTimezone($utc_timezone);

            return $date->format($format);
        }

        return null;
    }

    /**
     * @param $value
     * @param $format
     * @return null|string
     */
    public function convertDateFromUTC2TimeZone($value, $format = "Y-m-d H:i:s")
    {
        $time_zone_id = $this->TimeZone;
        if (empty($time_zone_id)) {
            return $value;
        }
        $time_zone_list = timezone_identifiers_list();

        if (isset($time_zone_list[$time_zone_id]) && !empty($value)) {
            $utc_timezone = new DateTimeZone("UTC");
            $time_zone_name = $time_zone_list[$time_zone_id];
            $time_zone = new DateTimeZone($time_zone_name);
            $date = new DateTime($value, $utc_timezone);

            $date->setTimezone($time_zone);

            return $date->format($format);
        }

        return null;
    }

    /**
     * @return string
     */
    public function getTimeZoneName(){
        $time_zone_id = $this->TimeZone;
        if (empty($time_zone_id)) {
            return 'Not Set';
        }
        $time_zone_list = timezone_identifiers_list();
        $time_zone_name = $time_zone_list[$time_zone_id];
        return $time_zone_name;
    }

    public function getTimeZoneOffsetFriendly(){
        $time_zone_id = $this->TimeZone;
        if (empty($time_zone_id)) {
            return 'Not Set';
        }
        $time_zone_list = timezone_identifiers_list();
        if(!isset($time_zone_list[$time_zone_id]))
            return 'Not Set';
        $time_zone_name = $time_zone_list[$time_zone_id];
        $tz = new DateTimeZone($time_zone_name);
        $now = new DateTime("now", $tz);
        $offset = $tz->getOffset( $now ) / 3600;
        return "GMT" . ($offset < 0 ? $offset : "+".$offset);

    }
}