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
    private static $db = [

        'Code'          => 'Varchar(255)',
        'EmailSent'     => 'Boolean',
        'Redeemed'      => 'Boolean',
        'Source'        => "Enum('CSV,ADMIN','CSV')",
        "EmailSentDate" => "SS_Datetime",
    ];

    private static $summary_fields = [
        'Code' => 'Code',
    ];

    private static $has_one = [
        'Summit'  => 'Summit',
        'Creator' => 'Member',
    ];

    private static $indexes = [
        'SummitID_Code' => array('type'=>'unique', 'value'=>'SummitID,Code')
    ];

    /**
     * @return SummitRegistrationPromoCode
     */
    public function markAsSent(){
        if($this->EmailSent) return $this;

        $this->EmailSent     = true;
        $this->EmailSentDate = CustomMySQLDatabase::nowRfc2822();
        return $this;
    }

    /**
     * @return bool
     */
    public function isEmailSent(){
        return $this->EmailSent;
    }

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
     * @return array()
     */
    static public function getTypes()
    {
        $speaker_types = singleton('SpeakerSummitRegistrationPromoCode')->dbObject('Type')->enumValues();
        $member_types = singleton('MemberSummitRegistrationPromoCode')->dbObject('Type')->enumValues();
        $types = array_merge($speaker_types,$member_types);

        $type_list = new ArrayList();
        foreach($types as $type) {
            $type_list->push(new ArrayData(array('Type' => $type)));
        }

        return $type_list;
    }

    /**
     * @return boolean
     */
    public function hasOwner(){ }

    public function setCode($code)
    {
        $this->setField('Code',$code);
    }

    public function setSummit($summit_id)
    {
        $this->SummitID = $summit_id;
    }

    public function setEmailSent($email_sent)
    {
        $this->setField('EmailSent',$email_sent);
    }

    public function setRedeemed($redeemed)
    {
        $this->setField('Redeemed',$redeemed);
    }

    public function setSource($source)
    {
        $this->setField('Source',$source);
    }

    public function setCreator($member)
    {
        $this->CreatorID = $member->ID;
    }
}