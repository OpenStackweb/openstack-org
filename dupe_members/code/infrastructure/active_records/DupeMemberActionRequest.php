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

/**
 * Class DupeMemberActionRequest
 */
class DupeMemberActionRequest
    extends DataObject
    implements IDupeMemberActionAccountRequest {

    static $create_table_options = array('MySQLDatabase' => 'ENGINE=InnoDB');

    static $db = array(
        'ConfirmationHash' => 'Text',
        'IsConfirmed'      => 'Boolean',
        'ConfirmationDate' => 'SS_Datetime',
    );

    static $has_one = array(
        'DupeAccount' => 'Member',
        'PrimaryAccount' => 'Member'
    );

    /**
     * @return string
     */
    public function getConfirmationHash()
    {
       return (string)$this->getField('ConfirmationHash');
    }

    /**
     * @return void
     */
    public function generateConfirmationHash()
    {
        $generator = new RandomGenerator();
        $token     = $generator->randomToken();
        $hash      = self::HashConfirmationToken($token);
        $this->setField('ConfirmationHash', $hash);
        return $token;
    }

    public static function HashConfirmationToken($token){
        return md5($token);
    }


    /**
     * @param ICommunityMember $member
     * @return void
     */
    public function registerPrimaryAccount(ICommunityMember $member)
    {
        AssociationFactory::getInstance()->getMany2OneAssociation($this, 'PrimaryAccount')->setTarget($member);
    }

    /**
     * @param ICommunityMember $member
     * @return void
     */
    public function registerDupeAccount(ICommunityMember $member)
    {
        AssociationFactory::getInstance()->getMany2OneAssociation($this, 'DupeAccount')->setTarget($member);
    }

    /**
     * @return ICommunityMember $member
     */
    public function getPrimaryAccount()
    {
        return AssociationFactory::getInstance()->getMany2OneAssociation($this, 'PrimaryAccount')->getTarget();
    }

    /**
     * @return ICommunityMember
     */
    public function getDupeAccount()
    {
        return AssociationFactory::getInstance()->getMany2OneAssociation($this, 'DupeAccount')->getTarget();
    }

    /**
     * @return bool
     */
    public function isVoid()
    {
       return $this->IsConfirmed;
    }

    /**
     * @return int
     */
    public function getIdentifier()
    {
        return (int)$this->getField('ID');
    }

    /**
     * @param string $token
     * @return bool
     * @throws DuperMemberActionRequestVoid
     * @throws InvitationAlreadyConfirmedException
     */
    public function doConfirmation($token)
    {
        $original_hash = $this->getField('ConfirmationHash');
        if($this->IsConfirmed) throw new DuperMemberActionRequestVoid;
        if(self::HashConfirmationToken($token) === $original_hash){
            $this->IsConfirmed      = true;
            $this->ConfirmationDate = SS_Datetime::now()->Rfc2822();
            return true;
        }
        throw new InvalidHashInvitationException;
    }
} 