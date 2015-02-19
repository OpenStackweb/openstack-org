<?php

/*
* Used to list important dates / deadlines on the summit details page
*/


class SummitLocation extends DataObject
{

    private static $db = array (
        'Type' => "Enum('Hotel,Airport,Venue')",
        'Order' => 'Int',
        'Name' => 'Text',
        'Address' => 'HTMLText',
        'Description' => 'HTMLText',
        'Latitude' => 'Text',
        'Longitude' => 'Text',
        'IsSoldOut' => 'Boolean',
        'Website' => 'Text',
        'BookingLink' => 'Text'
    );

    private static $has_one = array (
        'SummitLocationPage' => 'SummitLocationPage'
    );
    
    public function getCMSFields() {
        return FieldList::create(TabSet::create('Root'))
            ->dropdown('Type','Type', $this->dbObject('Type')->enumValues())            
            ->text('Name')
            ->textArea('Description')            
            ->textArea('Address')
            ->text('Latitude')
            ->text('Longitude')
            ->text('Website')
            ->text('BookingLink')
            ->checkbox('IsSoldOut','This location is <strong>sold out</strong> (applies to hotels only)');

    }     
            
}