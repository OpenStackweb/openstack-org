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

/**
 * Class SummitTicketType
 * https://www.eventbrite.com/developer/v3/endpoints/events/#ebapi-get-events-id-ticket-classes-ticket-class-id
 */
class SummitTicketType extends DataObject implements ISummitTicketType
{
    private static $db = [
        'ExternalId'                => 'Varchar(255)',
        'Name'                      => 'Text',
        'Description'               => 'Text',
        'Cost'                      => 'Currency',
        'Currency'                  => 'VarChar(3)',
        'QuantityToSell'            => 'Int',
        'QuantitySold'              => 'Int',
        'MaxQuantityToSellPerOrder' => 'Int',
        "SaleStartDate"             => "SS_Datetime",
        "SaleEndDate"               => "SS_Datetime",
    ];

    private static $has_one = [
        'Summit'    => 'Summit',
        'BadgeType' => 'SummitBadgeType',
    ];

    private static $has_many = [];

    private static $many_many = [
        'Taxes'    => 'SummitTaxType'
    ];

    static $indexes = array
    (
       'Summit_ExternalId' => array('type' => 'index', 'value' => 'SummitID,ExternalId')
    );

    private static $summary_fields = array
    (
        'Name'           => 'Name',
        'Cost'           => 'Cost',
        'Currency'       => 'Currency',
        'QuantityToSell' => 'QuantityToSell',
        'QuantitySold'   => 'QuantitySold',
        'ExternalId'     => 'ExternalId',
    );

    private static $searchable_fields = array
    (
    );


    // CMS admin UI
    public function getCMSFields()
    {

        $summit_id = isset($_REQUEST['SummitID']) ?  $_REQUEST['SummitID'] : $this->SummitID;

        $f = new FieldList
        (
            array
            (
                new HiddenField('SummitID', 'SummitID'),
                new TextField('Name', 'Name'),
                new TextField('Description', 'Description'),
                new CurrencyField('Cost', 'Cost'),
                new DropdownField('Currency','Currency (ISO 4217)', [
                    "USD" => "USD",
                    "EUR" => "EUR",
                    "GBP" => "GBP",
                ]),
                new NumericField('QuantityToSell', 'Quantity To Sell'),
                new NumericField('MaxQuantityToSellPerOrder', 'Max. Quantity To Sell Per Order'),
                $begin_sale = new DatetimeField('SaleStartDate', 'Sale Start Date (On Summit Time Zone)'),
                $end_sale = new DatetimeField('SaleEndDate', 'Sale End Date (On Summit Time Zone)'),
                new TextField('ExternalId', 'Eventbrite External Id'),
                $badge_type = new DropdownField('BadgeTypeID', 'Badge Type', SummitBadgeType::get()->filter("SummitID", $this->SummitID)->map('ID', 'Name'))
            )
        );

        $badge_type->setEmptyString("--SELECT BADGE--");

        $begin_sale->getDateField()->setConfig('showcalendar', true);
        $begin_sale->getDateField()->setConfig('dateformat', 'dd/MM/yyyy');
        $end_sale->getDateField()->setConfig('showcalendar', true);
        $end_sale->getDateField()->setConfig('dateformat', 'dd/MM/yyyy');

        return $f;
    }


    public function setSaleStartDate($value)
    {
        $summit_id  = isset($_REQUEST['SummitID']) ?  $_REQUEST['SummitID'] : $this->SummitID;
        $summit     = Summit::get()->byID($summit_id);
        if(is_null($summit)) throw new InvalidArgumentException('summit not found!');
        if(!empty($value))
        {
            $value = $summit->convertDateFromTimeZone2UTC($value);
            $this->setField('SaleStartDate', $value);
        }
    }

    /**
     * @return string
     */
    public function getSaleStartDate()
    {
        $summit_id  = isset($_REQUEST['SummitID']) ?  $_REQUEST['SummitID'] : $this->SummitID;
        $summit     = Summit::get()->byID($summit_id);
        if(is_null($summit)) throw new InvalidArgumentException('summit not found!');
        $value = $this->getField('SaleStartDate');
        return $summit->convertDateFromUTC2TimeZone($value);
    }

    public function setSaleEndDate($value)
    {
        $summit_id  = isset($_REQUEST['SummitID']) ?  $_REQUEST['SummitID'] : $this->SummitID;
        $summit     = Summit::get()->byID($summit_id);
        if(is_null($summit)) throw new InvalidArgumentException('summit not found!');
        if(!empty($value))
        {
            $value = $summit->convertDateFromTimeZone2UTC($value);
            $this->setField('SaleEndDate', $value);
        }
    }

    /**
     * @return string
     */
    public function getSaleEndDate()
    {
        $summit_id  = isset($_REQUEST['SummitID']) ?  $_REQUEST['SummitID'] : $this->SummitID;
        $summit     = Summit::get()->byID($summit_id);
        if(is_null($summit)) throw new InvalidArgumentException('summit not found!');
        $value = $this->getField('SaleEndDate');
        return $summit->convertDateFromUTC2TimeZone($value);
    }

    protected function validate()
    {
        return parent::validate();
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
    public function getExternalId()
    {
       return $this->getField('ExternalId');
    }

    /**
     * @return string
     */
    public function getName()
    {
       return $this->getField('Name');
    }

    /**
     * @return ISummit
     */
    public function getSummit()
    {
       return AssociationFactory::getInstance()->getMany2OneAssociation($this, 'Summit')->getTarget();
    }

    /**
     * @param Member $member
     * @return boolean
     */
    public function canView($member = null) {
        return Permission::check("ADMIN") || Permission::check("ADMIN_SUMMIT_APP") || Permission::check("ADMIN_SUMMIT_APP_SCHEDULE");
    }

    /**
     * @param Member $member
     * @return boolean
     */
    public function canEdit($member = null) {
        return Permission::check("ADMIN") || Permission::check("ADMIN_SUMMIT_APP") || Permission::check("ADMIN_SUMMIT_APP_SCHEDULE");
    }
}