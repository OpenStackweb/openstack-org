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
class Org extends DataObject implements IOrganization
{

    static $db = array
    (
        'Name'                   => 'Text',
        'IsStandardizedOrg'      => 'Boolean',
        'FoundationSupportLevel' => "Enum('Platinum Member, Gold Member, Silver Sponsor, Startup Sponsor, Supporting Organization')",
    );

    static $has_one = array(
        'OrgProfile' => 'Company'
    );

    static $has_many = array
    (
        'Members' => 'Member'
    );

    static $many_many = array
    (
        'InvolvementTypes' => 'InvolvementType'
    );

    static $singular_name = 'Org';
    static $plural_name   = 'Orgs';

    /**
     * @return int
     */
    public function getIdentifier()
    {
        return (int)$this->getField('ID');
    }

    public function onAfterWrite()
    {
        parent::onAfterWrite();
        //register new request
        $new_request                 = new OrganizationRegistrationRequest();
        $new_request->MemberID       = Member::currentUserID();
        $new_request->OrganizationID = $this->ID;
        $new_request->write();
    }
}