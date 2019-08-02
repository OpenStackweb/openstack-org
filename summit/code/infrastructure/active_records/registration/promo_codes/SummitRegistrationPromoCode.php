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
        'Code'              => 'Varchar(255)',
        'ExternalId'        => 'Varchar(255)',
        'EmailSent'         => 'Boolean',
        'Redeemed'          => 'Boolean',
        'Source'            => "Enum('CSV,ADMIN','CSV')",
        "EmailSentDate"     => "SS_Datetime",
        "QuantityAvailable" => "Int",
        "QuantityUsed"      => "Int",
        "ValidSinceDate"    => "SS_Datetime",
        "ValidUntilDate"    => "SS_Datetime",
    ];

    private static $summary_fields = [
        'Code' => 'Code',
    ];

    private static $has_one = [
        'Summit'    => 'Summit',
        'Creator'   => 'Member',
        'BadgeType' => 'SummitBadgeType',
    ];

    private static $indexes = [
        'SummitID_Code' => array('type'=>'unique', 'value'=>'SummitID,Code')
    ];

    private static $many_many = [
        'BadgeFeatures'      => 'SummitBadgeFeatureType',
        'AllowedTicketTypes' => 'SummitTicketType',
    ];

    // CMS admin UI
    public function getCMSFields()
    {
        $summit_id = isset($_REQUEST['SummitID']) ?  $_REQUEST['SummitID'] : $this->SummitID;

        $f = FieldList::create(TabSet::create('Root'));

        $f->addFieldsToTab("Root.Main",
            [
                new HiddenField('SummitID', 'SummitID'),
                new TextField('Code', 'Code'),
                new TextField('ExternalId', 'External Id'),
                new NumericField('QuantityAvailable', 'QuantityAvailable'),
                $begin_sale = new DatetimeField('ValidSinceDate', 'ValidSinceDate (On Summit Time Zone)'),
                $end_sale = new DatetimeField('ValidUntilDate', 'Valid Until Date (On Summit Time Zone)')
            ]);

        $begin_sale->getDateField()->setConfig('showcalendar', true);
        $begin_sale->getDateField()->setConfig('dateformat', 'dd/MM/yyyy');
        $end_sale->getDateField()->setConfig('showcalendar', true);
        $end_sale->getDateField()->setConfig('dateformat', 'dd/MM/yyyy');

        $badge_types = $this->Summit()->BadgeTypes()->map();
        $f->addFieldToTab("Root.Main", $ddl_badge_types = new DropdownField("BadgeTypeID", "BadgeType", $badge_types));
        $ddl_badge_types->setEmptyString('-- select a badge type --');

        if($this->ID > 0) {
            $config = GridFieldConfig_RelationEditor::create(50);
            $completer = $config->getComponentByType('GridFieldAddExistingAutocompleter');
            $completer->setResultsFormat('$Name ($ID)');
            $completer->setSearchFields(['Name', 'ID']);
            $completer->setSearchList(SummitBadgeFeatureType::get()->filter('SummitID', $summit_id));
            $badge_features = new GridField('BadgeFeatures', 'Badge Features', $this->BadgeFeatures(), $config);
            $f->addFieldToTab('Root.Badge Features', $badge_features);

            $config = GridFieldConfig_RelationEditor::create(50);
            $completer = $config->getComponentByType('GridFieldAddExistingAutocompleter');
            $completer->setResultsFormat('$Name ($ID)');
            $completer->setSearchFields(['Name', 'ID']);
            $completer->setSearchList($this->AllowedTicketTypes());
            $ticket_types = new GridField('AllowedTicketTypes', 'Allowed Ticket Types', $this->AllowedTicketTypes(), $config);
            $f->addFieldToTab('Root.Allowed Ticket Types', $ticket_types);


        }
        return $f;
    }

    public function setValidSinceDate($value)
    {
        $summit_id  = isset($_REQUEST['SummitID']) ?  $_REQUEST['SummitID'] : $this->SummitID;
        $summit     = Summit::get()->byID($summit_id);
        if(is_null($summit)) throw new InvalidArgumentException('summit not found!');
        if(!empty($value))
        {
            $value = $summit->convertDateFromTimeZone2UTC($value);
            $this->setField('ValidSinceDate', $value);
        }
    }

    /**
     * @return string
     */
    public function getValidSinceDate()
    {
        $summit_id  = isset($_REQUEST['SummitID']) ?  $_REQUEST['SummitID'] : $this->SummitID;
        $summit     = Summit::get()->byID($summit_id);
        if(is_null($summit)) throw new InvalidArgumentException('summit not found!');
        $value = $this->getField('ValidSinceDate');
        return $summit->convertDateFromUTC2TimeZone($value);
    }

    public function setValidUntilDate($value)
    {
        $summit_id  = isset($_REQUEST['SummitID']) ?  $_REQUEST['SummitID'] : $this->SummitID;
        $summit     = Summit::get()->byID($summit_id);
        if(is_null($summit)) throw new InvalidArgumentException('summit not found!');
        if(!empty($value))
        {
            $value = $summit->convertDateFromTimeZone2UTC($value);
            $this->setField('ValidUntilDate', $value);
        }
    }

    /**
     * @return string
     */
    public function getValidUntilDate()
    {
        $summit_id  = isset($_REQUEST['SummitID']) ?  $_REQUEST['SummitID'] : $this->SummitID;
        $summit     = Summit::get()->byID($summit_id);
        if(is_null($summit)) throw new InvalidArgumentException('summit not found!');
        $value = $this->getField('ValidUntilDate');
        return $summit->convertDateFromUTC2TimeZone($value);
    }


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