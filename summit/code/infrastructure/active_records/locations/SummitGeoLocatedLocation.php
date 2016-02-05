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
class SummitGeoLocatedLocation extends SummitAbstractLocation implements ISummitGeoLocatedLocation
{

    private static $db = array
    (
        'Address1'        => 'Text',
        'Address2'        => 'Text',
        'ZipCode'         => 'Text',
        'City'            => 'Text',
        'State'           => 'Text',
        'Country'         => 'Text',
        'WebSiteUrl'      => 'Text',
        'Lng'             => 'Text',
        'Lat'             => 'Text',
        'DisplayOnSite'   => 'Boolean',
        'DetailsPage'     => 'Boolean',
        'LocationMessage' => 'HTMLText',
    );

    private static $has_many = array
    (
        'Maps' => 'SummitLocationMap'
    );

    private static $defaults = array
    (
        'DisplayOnSite' => false
    );

    private static $summary_fields = array
    (
    );

    private static $searchable_fields = array
    (
    );

    /**
     * @return string
     */
    public function getAddress()
    {
       $address = $this->Address1;
       if(!empty($this->Address2))
           $address .= ', '.$this->Address2;
        if(!empty($this->City))
            $address .= ', '.$this->City;
        if(!empty($this->State))
            $address .= ', '.$this->State;
        if(!empty($this->Country))
            $address .= ', '.$this->Country;
       return $address;
    }

    /**
     * @return int
     */
    public function getLng()
    {
        return (float)$this->getField('Lng');
    }

    /**
     * @return int
     */
    public function getLat()
    {
        return (float)$this->getField('Lat');
    }

    /**
     * @return string
     */
    public function getWebsiteUrl()
    {
        return $this->getField('WebSiteUrl');
    }

    /**
     * @return bool
     */
    public function canDisplayOnSite()
    {
        return $this->getField('DisplayOnSite');
    }

    /**
     * @param IGeoCodingService $geo_service
     * @return void
     */
    public function setCoordinates(IGeoCodingService $geo_service)
    {

       list($lat, $lng) = $geo_service->getAddressCoordinates
       (
           new AddressInfo
           (
               $this->getAddress(),
               '',
               $this->getZipCode(),
               $this>getState(),
               $this->getCity(),
               $this->getCountry()
           )
       );

       $this->Lat = $lat;
       $this->Lng = $lng;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->getField('Country');
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->getField('City');
    }

    /**
     * @return string
     */
    public function getZipCode()
    {
        return $this->getField('ZipCode');
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->getField('State');
    }

    public function getCMSFields()
    {
        $f = parent::getCMSFields();
        $f->addFieldToTab('Root.Main', new TextField('WebSiteUrl','WebSite Url'));
        $f->addFieldToTab('Root.Main', new CheckboxField('DisplayOnSite','Should Display On Site'));
        $f->addFieldToTab('Root.Main', $messageField = new TextField('LocationMessage','Message to display for this location'));
        $messageField->setAttribute( 'style', 'max-width:100% !important' );
        $f->addFieldToTab('Root.Main', new CheckboxField('DetailsPage','Send people to a details page first?'));
        
        $f->addFieldsToTab("Root.Location", array
        (
            // Create hidden latitude field
            HiddenField::create("Lat"),
            // Create hidden longitude field
            HiddenField::create("Lng"),
            TextField::create("Address1"),
            TextField::create("Address2"),
            TextField::create("ZipCode"),
            TextField::create("City"),
            TextField::create("State"),
            CountryDropdownField::create("Country"),
            // Create Google map field
            GoogleMapField::create("Map", array
            (
                "height"        => "500px",
                "width"         => "600px",
                "lng_field"     => "Form_ItemEditForm_Lng",
                "lat_field"     => "Form_ItemEditForm_Lat",
                "address_field" => array
                (
                    'address1' => "Address1",
                    'zip_code' => "ZipCode" ,
                    'city'     => "City",
                    'state'    => "State",
                    'country' => "Country"
                ),
                'start_lat' => $this->Lat,
                'start_lng' => $this->Lng,
            ))
        ));

        if($this->ID > 0)
        {
            $config = GridFieldConfig_RecordEditor::create();
            $gridField = new GridField('Maps', 'Maps', $this->Maps(), $config);
            $config->addComponent($sort = new GridFieldSortableRows('Order'));
            $f->addFieldToTab('Root.Maps', $gridField);
        }

        return $f;
    }

    /**
     * @return string[]
     */
    public function getMapsUrls()
    {
       $urls = array();
       foreach($this->Maps() as $map)
       {
           array_push($urls, $map->getUrl());
       }
        return $urls;
    }
}