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
class MemberSummitRegistrationPromoCode extends SummitRegistrationPromoCode implements IMemberSummitRegistrationPromoCode
{
    private static $db = array
    (
        'FirstName' => 'Varchar',
        'LastName'  => 'Varchar',
        'Email'     => 'Varchar(254)',
        'Type'      => "Enum('VIP,ATC,MEDIA ANALYST,SPONSOR','VIP')",
    );

    private static $has_one = array
    (
        'Owner' => 'Member',
    );

    public function validate() {
        $result = parent::validate();
        $promocode_repository = new SapphireSummitRegistrationPromoCodeRepository();

        if ($this->OwnerID) {
            $has_code_assigned = $promocode_repository->getByOwner($this->Summit()->getIdentifier(),$this->Owner()->getIdentifier());
            foreach($has_code_assigned as $code_taken) {
                if ($code_taken->ID != $this->ID)
                    $result->error(sprintf('Member already assigned to another promo code: %s', $code_taken->Code));
            }
        }

        if ($this->Email) {
            $has_code_assigned = $promocode_repository->getByEmail($this->Summit()->getIdentifier(),$this->Email);
            foreach($has_code_assigned as $code_taken) {
                if ($code_taken->ID != $this->ID)
                    $result->error(sprintf('Email is already assigned to another promo code: %s', $code_taken->Code));
            }
        }

        return $result;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->getField('Type');
    }

    /**
     * @return ICommunityMember
     */
    public function getOwner()
    {
        return AssociationFactory::getInstance()->getMany2OneAssociation($this,'Owner')->getTarget();
    }

    /**
     * @param ICommunityMember $member
     * @return $this
     */
    public function assignOwner(ICommunityMember $member)
    {
        $this->OwnerID = $member->getIdentifier();
        AssociationFactory::getInstance()->getMany2OneAssociation($this,'Owner')->setTarget($member);
    }

    public function setFirstName($first_name)
    {
        $this->setField('FirstName',$first_name);
    }

    public function setLastName($last_name)
    {
        $this->setField('LastName',$last_name);
    }

    public function setEmail($email)
    {
        $this->setField('Email',$email);
    }

    public function setType($type)
    {
        $this->setField('Type',$type);
    }
}