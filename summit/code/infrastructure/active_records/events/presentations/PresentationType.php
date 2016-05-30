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
class PresentationType extends SummitEventType
{

    private static $db = array
    (
        'MaxSpeakers'   => 'Int',
        'MinSpeakers'   => 'Int',
        'MaxModerators' => 'Int',
        'MinModerators' => 'Int'
    );

    private static $has_many = array
    (
    );

    private static $defaults = array
    (
    );

    private static $has_one = array
    (
    );

    private static $summary_fields = array
    (
    );

    private static $searchable_fields = array
    (
    );
    /**
     * @return int
     */
    public function getIdentifier()
    {
        return (int)$this->getField('ID');
    }

    public function getCMSFields() {
        $fields = parent::getCMSFields();
        $allowed_types = new DropdownField('Type', 'Type',
            array('Presentation' => 'Presentation', 'Keynotes' => 'Keynotes', 'Panel'=> 'Panel')
        );
        $fields->replaceField('Type', $allowed_types );
        $fields->add(new TextField("MaxSpeakers","Max Speakers"));
        $fields->add(new TextField("MinSpeakers","Min Speakers"));
        $fields->add(new TextField("MaxModerators","Max Moderators"));
        $fields->add(new TextField("MinModerators","Min Moderators"));
        return $fields;
    }

    public function getMaxSpeakers() {
        return $this->getField('MaxSpeakers');
    }

    public function getMinSpeakers() {
        return $this->getField('MinSpeakers');
    }

    public function getMaxModerators() {
        return $this->getField('MaxModerators');
    }

    public function getMinModerators() {
        return $this->getField('MinModerators');
    }

}