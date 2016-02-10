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

/**
 * Class SpeakerTravelPreference
 */
class SpeakerTravelPreference
    extends DataObject
    implements ISpeakerTravelPreference {

    private static $db = array(
        'Country'      => 'Text'
    );

    private static $has_one = array
    (
        'Speaker' => 'PresentationSpeaker',
    );

    private static $summary_fields = array
    (
        'Country' => 'Country',
    );

    /**
     * @return int
     */
    public function getIdentifier()
    {
        return (int)$this->getField('ID');
    }

    public function getCountryName() {
        $country = '';

        if ($this->Country) {
            $country = CountryCodes::countryCode2name($this->Country);
        }

        return $country;
    }
}