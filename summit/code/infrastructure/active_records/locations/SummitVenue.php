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
class SummitVenue extends SummitGeoLocatedLocation implements ISummitVenue
{

    private static $db = array
    (
    );

    private static $has_many = array
    (
        'Rooms' => 'SummitVenueRoom',
    );

    private static $defaults = array
    (
    );


    private static $summary_fields = array
    (
    );

    private static $searchable_fields = array
    (
    );


    /**
     * @return bool
     */
    public function hasRooms()
    {
       return count($this->getRooms()) > 0;
    }

    /**
     * @return ISummitVenueRoom[]
     */
    public function getRooms()
    {
        return AssociationFactory::getInstance()->getOne2ManyAssociation($this, 'Rooms')->toArray();
    }

    /**
     * @param ISummitVenueRoom $room
     * @return void
     */
    public function addRoom(ISummitVenueRoom $room)
    {
        AssociationFactory::getInstance()->getOne2ManyAssociation($this, 'Rooms')->add($room);
    }

    /**
     * @return void
     */
    public function clearAllRooms()
    {
        AssociationFactory::getInstance()->getOne2ManyAssociation($this, 'Rooms')->removeAll();
    }

    public function getCMSFields()
    {
        $f = parent::getCMSFields();

        $map_field = new UploadField('Map','Map');
        $map_field->setAllowedMaxFileNumber(1);
        $map_field->setFolderName(sprintf('summits/%s/locations/venues/maps/', $this->SummitID));

        $f->addFieldToTab('Root.Main',$map_field );

        $config = GridFieldConfig_RecordEditor::create();
        $gridField = new GridField('Rooms', 'Rooms', $this->Rooms(), $config);
        $f->addFieldToTab('Root.Rooms', $gridField);

        return $f;
    }

    public function getTypeName()
    {
        return 'Venue';
    }

    public function inferLocationType()
    {
        return 'Internal';
    }


}