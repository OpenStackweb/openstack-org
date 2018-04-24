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

/**
 * Class ScheduledSummitLocationBanner
 */
final class ScheduledSummitLocationBanner extends SummitLocationBanner
{
    private static $db = [
        'StartDate' => 'SS_Datetime',
        'EndDate'   => 'SS_Datetime',
    ];

    protected function validate()
    {
        $valid = parent::validate();

        if(!$valid->valid()) return $valid;

        $start_date = $this->getStartDate();
        $end_date   = $this->getEndDate();

        if((empty($start_date) || empty($end_date)) && $this->Enabled)
            return $valid->error('To Enabled this Banner you must define a start/end datetime!');

        if(!empty($start_date) && !empty($end_date)) {
            $summit   = $this->Location()->Summit();
            $timezone = $summit->TimeZoneIdentifier;

            if (empty($timezone)) {
                return $valid->error('Invalid Summit TimeZone!');
            }

            $start_date = new DateTime($start_date);
            $end_date   = new DateTime($end_date);

            if ($end_date <= $start_date)
                return $valid->error('start datetime must be greather than end datetime!');
        }

        return $valid;
    }

    /**
     * @return string
     */
    public function getStartDate()
    {
        $location_id = $this->LocationID > 0 ? $this->LocationID : $_REQUEST['LocationID'];
        $location    = SummitAbstractLocation::get()->byID($location_id);
        $summit      = $location->Summit();
        $value       = $this->getField('StartDate');
        return $summit->convertDateFromUTC2TimeZone($value);
    }

    /**
     * @return string
     */
    public function getEndDate()
    {
        $location_id = $this->LocationID > 0 ? $this->LocationID : $_REQUEST['LocationID'];
        $location    = SummitAbstractLocation::get()->byID($location_id);
        $summit      = $location->Summit();
        $value = $this->getField('EndDate');
        return $summit->convertDateFromUTC2TimeZone($value);
    }

    public function setStartDate($value)
    {
        $location_id = $this->LocationID > 0 ? $this->LocationID : $_REQUEST['LocationID'];
        $location    = SummitAbstractLocation::get()->byID($location_id);
        $summit      = $location->Summit();
        if(is_null($summit)) throw new InvalidArgumentException('summit not found!');
        if(!empty($value))
        {
            $value = $summit->convertDateFromTimeZone2UTC($value);
            $this->setField('StartDate', $value);
        }
    }

    public function setEndDate($value)
    {
        $location_id = $this->LocationID > 0 ? $this->LocationID : $_REQUEST['LocationID'];
        $location    = SummitAbstractLocation::get()->byID($location_id);
        $summit      = $location->Summit();
        if(is_null($summit)) throw new InvalidArgumentException('summit not found!');
        if(!empty($value))
        {
            $value = $summit->convertDateFromTimeZone2UTC($value);
            $this->setField('EndDate', $value);
        }
    }

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $summit_time_zone = null;
        $location_id = $this->LocationID > 0 ? $this->LocationID : $_REQUEST['LocationID'];
        $location    = SummitAbstractLocation::get()->byID($location_id);
        $summit      = $location->Summit();
        if($summit->TimeZoneIdentifier) {
            $summit_time_zone = $summit->TimeZoneIdentifier;
        }

        if($summit_time_zone) {
            $fields->addFieldToTab('Root.Dates', new HeaderField("All dates below are in <span style='color:red;'>$summit_time_zone</span> time."));
        }
        else {
            $fields->addFieldToTab('Root.Dates', new HeaderField("All dates below in the timezone of the summit's venue."));
        }

        $fields->addFieldToTab('Root.Dates', $date = new DatetimeField('StartDate', "When does this banner start to be show?"));
        $date->getDateField()->setConfig('showcalendar', true);
        $date->getDateField()->setConfig('dateformat', 'dd/MM/yyyy');
        $fields->addFieldToTab('Root.Dates', $date = new DatetimeField('EndDate', "When does this Banner should stop to be shown?"));
        $date->getDateField()->setConfig('showcalendar', true);
        $date->getDateField()->setConfig('dateformat', 'dd/MM/yyyy');

        return $fields;
    }

}