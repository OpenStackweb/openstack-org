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
        'BookingLink' => 'Text',
        'DisplayOnSite' => 'Boolean'
    );

    private static $has_one = array (
        'SummitLocationPage' => 'SummitLocationPage'
    );
    
    public function getCMSFields() {

        $fields =  FieldList::create(TabSet::create('Root'));
        $fields->addFieldToTab('Root.Main', new DropdownField('Type','Type', $this->dbObject('Type')->enumValues()));
        $fields->addFieldToTab('Root.Main', new TextField('Name','Name'));
        $fields->addFieldToTab('Root.Main', new TextareaField('Description','Description'));
        $fields->addFieldToTab('Root.Main', new TextareaField('Address','Address'));
        $fields->addFieldToTab('Root.Main', new TextField('Latitude','Latitude'));
        $fields->addFieldToTab('Root.Main', new TextField('Longitude','Longitude'));
        $fields->addFieldToTab('Root.Main', new TextField('Website','Website'));
        $fields->addFieldToTab('Root.Main', new TextField('BookingLink','BookingLink'));
        $fields->addFieldToTab('Root.Main', new CheckboxField('IsSoldOut','This location is <strong>sold out</strong> (applies to hotels only)'));
        $fields->addFieldToTab('Root.Main', new CheckboxField('DisplayOnSite','Show this location on the website. Will be hidden if unchecked.'));
        return $fields;
    }
            
}