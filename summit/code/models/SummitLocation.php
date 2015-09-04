<?php

/*
* Used to list important dates / deadlines on the summit details page
*/


class SummitLocation extends DataObject
{

    private static $db = array (
        'Type' => "Enum('Hotel,AlternateHotel,Airport,Venue')",
        'Order' => 'Int',
        'Name' => 'Text',
        'Address' => 'HTMLText',
        'Description' => 'HTMLText',
        'Latitude' => 'Text',
        'Longitude' => 'Text',
        'IsSoldOut' => 'Boolean',
        'Website' => 'Text',
        'BookingLink' => 'Text',
        'DisplayOnSite' => 'Boolean',
        'BookingStartDate' => 'SS_DateTime',
        'BookingEndDate' => 'SS_DateTime',
        'InRangeBookingGraphic' => 'Text',
        'OutOfRangeBookingGraphic' => 'Text',
        'DetailsPage' => 'Boolean',
        'LocationMessage' => 'Text',
        'PublicTransitInstructions' => 'HTMLText',
        'DistanceFromVenue' => 'Text'
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
        $fields->addFieldToTab('Root.Main', new TextField('LocationMessage','Message to display for this location'));        
        $fields->addFieldToTab('Root.Main', new TextField('Website','Website'));
        $fields->addFieldToTab('Root.Main', new TextField('BookingLink','BookingLink'));

        $start_date = new DatetimeField('BookingStartDate', 'Booking Block - Start Date');
        $start_date->getDateField()->setConfig('showcalendar', true);
        $start_date->setConfig('dateformat', 'dd/MM/yyyy');
        $fields->addFieldToTab('Root.Main', $start_date);

        $end_date = new DatetimeField('BookingEndDate', 'Booking Block - End Date');
        $end_date->getDateField()->setConfig('showcalendar', true);
        $end_date->setConfig('dateformat', 'dd/MM/yyyy');
        $fields->addFieldToTab('Root.Main', $end_date);

        $fields->addFieldToTab('Root.Main', new TextField('InRangeBookingGraphic','URL of graphic of an in range stay'));
        $fields->addFieldToTab('Root.Main', new TextField('OutOfRangeBookingGraphic','URL of graphic of an out of range stay'));
        $fields->addFieldToTab('Root.Main', new CheckboxField('IsSoldOut','This location is <strong>sold out</strong> (applies to hotels only)'));
        $fields->addFieldToTab('Root.Main', new CheckboxField('DisplayOnSite','Show this location on the website. Will be hidden if unchecked.'));
        $fields->addFieldToTab('Root.Main', new CheckboxField('DetailsPage','Send people to a details page first?'));
        $fields->addFieldToTab('Root.Main', new TextField('DistanceFromVenue','Distance From Venue'));
        $fields->addFieldToTab('Root.Main', new TextField('PublicTransitInstructions','Public Transit Instructions'));

        return $fields;
    }
            
}