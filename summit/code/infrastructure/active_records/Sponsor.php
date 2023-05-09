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

    static $db = array
    (
        'SubmitPageUrl' => 'Text',
        'Order'         => 'Int'
    );

    static $has_one = array
    (
        'Company'              => 'Company',
        'Summit'                => 'Summit',
        'SummitSponsorshipType' => 'Summit_SponsorshipType',
    );

    private static $many_many = [
        'Users' => 'Member',
    ];

    private static $has_many = [
        'BadgeScans' => 'SponsorBadgeScan'
    ];

    private static $searchable_fields = array
    (
        'Company.Name'
    );

    /**
     * @var array
     */
    private static $summary_fields = array
    (
        'ID'                      => 'ID',
        'Company.Name'            => 'Company',
    );

    /**
     * @return int
     */
    public function getIdentifier()
    {
        return (int)$this->getField('ID');
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