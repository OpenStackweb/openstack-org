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
class SummitVenueFloor extends DataObject implements ISummitVenueFloor
{

    private static $db = array
    (
        'Name' => 'Varchar',
        'Description' => 'Text',
        'Number' => 'Int',
    );

    private static $has_many = array
    (
        'Rooms' => 'SummitVenueRoom',
    );

    private static $has_one = array
    (
        'Venue' => 'SummitVenue',
        'Image' => 'BetterImage',
    );


    private static $summary_fields = array
    (
        'Name',
        'Number',
        'Venue.Name',
    );

    /**
     * @return int
     */
    public function getIdentifier()
    {
        return (int)$this->getField('ID');
    }

    public function getCMSFields()
    {
        $f = new FieldList();
        $f->add(new TextField('Name','Name'));
        $f->add(new TextareaField('Description','Description'));
        $f->add(new NumericField('Number','Number'));
        $f->add(new UploadField('Image','Image'));
        $f->add(new HiddenField('VenueID','VenueID'));

        if ($this->ID) {
            $f->add(new LiteralField('br','<br>'));
            $config = GridFieldConfig_RelationEditor::create();
            $config->getComponentByType('GridFieldAddExistingAutocompleter')->setSearchList($this->Venue()->Rooms());
            $gridField = new GridField('Rooms', 'Rooms', $this->Rooms(), $config);
            $f->add($gridField);
        }

        return $f;
    }

    public function getTypeName()
    {
        return 'VenueFloor';
    }

    public function getVenue() {
        return $this->Venue();
    }

    protected function onBeforeDelete()
    {
        parent::onBeforeDelete();
        // reset all rooms assigned to this deleted floor
        DB::query("UPDATE SummitVenueRoom SET FloorID = 0 WHERE FloorID = {$this->ID};");
    }
}