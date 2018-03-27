<?php

/**
 * Copyright 2014 Openstack Foundation
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
class Sponsor extends DataObject implements ISponsor
{

    private static $create_table_options = array('MySQLDatabase' => 'ENGINE=MyISAM');

    static $db = array
    (
        'SubmitPageUrl' => 'Text',
        'Order'         => 'Int'
    );

    static $has_one = array
    (
        'Company'         => 'Company',
        'SponsorshipType' => 'SponsorshipType',
        'Summit'          => 'Summit'
    );

    private static $searchable_fields = array
    (
        'Company.Name'
    );

    /**
     * @return int
     */
    public function getIdentifier()
    {
        return (int)$this->getField('ID');
    }

    //helper function to create Drop Down for Sponsorship type
    public function getDDLSponsorshipType()
    {
        $types = SponsorshipType::get()->map();
        $type = $this->SponsorshipTypeID;
        return new DropdownField("SponsorshipType_{$this->ID}", "SponsorshipType_{$this->ID}", $types, $type);
    }

    public function getSubmitPageUrl()
    {
        if ($this->getField('SubmitPageUrl')) return $this->getField('SubmitPageUrl');
        else return $this->Company()->URL;
    }

    public function getInputSubmitPageUrl()
    {
        return new TextField("SubmitPageUrl_{$this->ID}", "SubmitPageUrl_{$this->ID}", $this->getSubmitPageUrl(), 255);
    }
}