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
        'Code'      => 'Varchar(255)',
        'EmailSent' => 'Boolean',
        'Redeemed'  => 'Boolean',
        'Source'    => "Enum('CSV,ADMIN','CSV')",
    );

    private static $summary_fields = array (
        'Code' => 'Code',
    );

    private static $has_one = array(
        'Summit'  => 'Summit',
        'Creator' => 'Member',
    );

    private static $indexes = array(
        'SummitID_Code' => array('type'=>'unique', 'value'=>'SummitID,Code')
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