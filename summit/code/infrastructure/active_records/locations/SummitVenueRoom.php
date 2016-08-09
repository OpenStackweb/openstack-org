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
class SummitVenueRoom extends SummitAbstractLocation implements ISummitVenueRoom
{

    private static $db = array
    (
        'Capacity'          => 'Int',
        'OverrideBlackouts' => 'Boolean',
    );

    private static $has_many = array
    (
    );

    private static $has_one = array
    (
        'Venue' => 'SummitVenue',
        'Floor' => 'SummitVenueFloor',
        'Image' => 'BetterImage',
    );

    public function getFullName()
    {
        if ($this->Floor()->exists())
            return sprintf('%s - %s - %s', $this->Floor()->Venue()->Name, $this->Floor()->Name, $this->Name);
        else
            return sprintf('%s - %s', $this->Venue()->Name, $this->Name);
    }

    private static $summary_fields = array
    (
        'Name',
        'Capacity',
        'FloorName',
        'OverrideBlackouts',
    );

    private static $searchable_fields = array
    (
    );
    /**
     * @return int
     */
    public function getCapacity()
    {
        return $this->getField('Capacity');
    }

    /**
     * @return bool
     */
    public function isFull()
    {
        return false;
    }

    public function overridesBlackouts() {
        return $this->OverrideBlackouts;
    }

    public function getFloorName(){
        return $this->Floor()->exists()?$this->Floor()->getFullName() : 'NOT SET';
    }

    public function getCMSFields()
    {
        $f = parent::getCMSFields();
        // hack
        $this->SummitID = $_REQUEST['SummitID'];
        $this->VenueID  = $_REQUEST['VenueID'];
        $f->addFieldToTab('Root.Main', new NumericField('Capacity','Capacity'));
        $f->addFieldToTab('Root.Main', new CheckboxField('OverrideBlackouts','Overrides Blackouts'));
        $f->addFieldToTab('Root.Main', new HiddenField('VenueID','VenueID'));
        $f->addFieldToTab('Root.Main', $ddl_floor = new DropdownField('FloorID','Floor', SummitVenueFloor::get()->filter('VenueID', $this->VenueID )->map("ID", "FullName")));
        $ddl_floor->setEmptyString("-- SELECT A FLOOR ");
        $f->addFieldToTab('Root.Main', new UploadField('Image','Map'));
        return $f;
    }

    public function getTypeName()
    {
        return 'VenueRoom';
    }

    public function getVenue() {
        return $this->Venue();
    }

    public function getLink() {
        return parent::getLink().'/#room='.$this->ID;
    }
}