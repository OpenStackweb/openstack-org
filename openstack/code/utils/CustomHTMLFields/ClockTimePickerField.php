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
        Requirements::javascript('themes/openstack/bower_assets/clockpicker/dist/jquery-clockpicker.js');
        Requirements::javascript('openstack/code/utils/CustomHTMLFields/js/ClockTimePickerField.js');
        Requirements::css('themes/openstack/bower_assets/clockpicker/dist/jquery-clockpicker.min.css');
        Requirements::css('openstack/code/utils/CustomHTMLFields/css/ClockTimePickerField.css');

        $this->addExtraClass('ss-timeclock-field');

        return $this
            ->customise($properties)
            ->renderWith(array("ClockTimePickerField"));
    }
}