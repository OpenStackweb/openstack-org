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
class ClockTimePickerField extends TimeField
{

    public function Field($properties = array())
    {
        Requirements::javascript('openstack/code/utils/CustomHTMLFields/js/jquery-clockpicker.js');
        Requirements::javascript('openstack/code/utils/CustomHTMLFields/js/ClockTimePickerField.js');
        Requirements::css('themes/openstack/bower_assets/clockpicker/dist/jquery-clockpicker.min.css');
        Requirements::css('openstack/code/utils/CustomHTMLFields/css/ClockTimePickerField.css');

        $this->addExtraClass('ss-timeclock-field');
        $this->locale = 'en_US';

        return $this
            ->customise($properties)
            ->renderWith(array("ClockTimePickerField"));


    }

    public function setValue($val) {

        // Fuzzy matching through strtotime() to support a wider range of times,
        // e.g. 11am. This means that validate() might not fire.
        // Note: Time formats are assumed to be less ambiguous than dates across locales.
        if($this->getConfig('use_strtotime') && !empty($val)) {
            if($parsedTimestamp = strtotime($val)) {
                $parsedObj = new Zend_Date($parsedTimestamp, Zend_Date::TIMESTAMP, $this->locale);
                $val = $parsedObj->get($this->getConfig('timeformat'));
                unset($parsedObj);
            }
        }

        if(empty($val)) {
            $this->value = null;
            $this->valueObj = null;
        }
        // load ISO time from database (usually through Form->loadDataForm())
        // Requires exact format to prevent false positives from locale specific times
        else if($this->valueObj = $this->parseTime($val, $this->getConfig('datavalueformat'), $this->locale, true)) {
            $this->value = $this->valueObj->get($this->getConfig('timeformat'));
        }
        // Set in current locale (as string)
        else if($this->valueObj = $this->parseTime($val, $this->getConfig('timeformat'), $this->locale)) {
            $this->value = $this->valueObj->get($this->getConfig('timeformat'));
        }
        // Fallback: Set incorrect value so validate() can pick it up
        elseif(is_string($val)) {
            $this->value = $val;
            $this->valueObj = null;
        }
        else {
            $this->value = null;
            $this->valueObj = null;
        }

        return $this;
    }

}