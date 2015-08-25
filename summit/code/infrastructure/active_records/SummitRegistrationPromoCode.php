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
class SummitRegistrationPromoCode extends DataObject implements ISummitRegistrationPromoCode
{
    private static $db = array
    (
        'Code' => 'Varchar(255)',
    );

    private static $summary_fields = array (
        'Code' => 'Code',
    );

    private static $has_one = array(
        'Summit' => 'Summit',
        'Owner'  => 'Member'
    );

    private static $indexes = array(
        'SummitID_Code_Owner' => array('type'=>'unique', 'value'=>'SummitID,Code, OwnerID')
    );

    /**
     * @return int
     */
    public function getIdentifier()
    {
        return (int)$this->getField('ID');
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->getField('Code');
    }

    /**
     * @return Summit
     */
    public function getSummit()
    {
        return AssociationFactory::getInstance()->getMany2OneAssociation($this,'Summit')->getTarget();
    }

    /**
     * @return ICommunityMember
     */
    public function getOwner()
    {
        return AssociationFactory::getInstance()->getMany2OneAssociation($this,'Owner')->getTarget();
    }

    /**
     * @param ICommunityMember $owner
     * @return $this
     */
    public function assignOwner(ICommunityMember $owner)
    {
        AssociationFactory::getInstance()->getMany2OneAssociation($this,'Owner')->setTarget($owner);
    }
}