<?php
/**
 * Copyright 2019 OpenStack Foundation
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
 * Class SummitRoomReservation
 */
class SummitRoomReservation extends DataObject
{
    private static $db = [
        'StartDateTime'             => 'SS_Datetime',
        'EndDateTime'               => 'SS_Datetime',
        'Status'                    => 'Text',
        'PaymentGatewayCartId'      => 'VarChar(512)',
        'PaymentGatewayClientToken' => 'Text',
        'Currency'                  => 'VarChar(3)',
        'Amount'                    => 'Int',
        'RefundedAmount'            => 'Int',
        'ApprovedPaymentDate'       => 'SS_Datetime',
        'LastError'                 => 'Text',
    ];

    private static $has_one = [
        'Owner' => 'Member',
        'Room' => 'SummitBookableVenueRoom',
    ];

    public function setStartDateTime($value)
    {
        $summit_id  = isset($_REQUEST['SummitID']) ?  $_REQUEST['SummitID'] : $this->Room()->Venue()->SummitID;
        $summit     = Summit::get()->byID($summit_id);
        if(is_null($summit)) throw new InvalidArgumentException('summit not found!');
        if(!empty($value))
        {
            $value = $summit->convertDateFromTimeZone2UTC($value);
            $this->setField('StartDateTime', $value);
        }
    }

    public function setEndDateTime($value)
    {
        $summit_id  = isset($_REQUEST['SummitID']) ?  $_REQUEST['SummitID'] : $this->Room()->Venue()->SummitID;
        $summit     = Summit::get()->byID($summit_id);
        if(is_null($summit)) throw new InvalidArgumentException('summit not found!');
        if(!empty($value))
        {
            $value = $summit->convertDateFromTimeZone2UTC($value);
            $this->setField('EndDateTime', $value);
        }
    }

    /**
     * @return string
     */
    public function getStartDateTime()
    {
        $summit_id  = isset($_REQUEST['SummitID']) ?  $_REQUEST['SummitID'] :  $this->Room()->Venue()->SummitIDD;
        $summit     = Summit::get()->byID($summit_id);
        if(is_null($summit)) throw new InvalidArgumentException('summit not found!');
        $value = $this->getField('StartDateTime');
        return $summit->convertDateFromUTC2TimeZone($value);
    }

    /**
     * @return string
     */
    public function getStartDateTimeUTC(){
        return  $this->getField('StartDateTime');
    }

    /**
     * @return string
     */
    public function getEndDateTime()
    {
        $summit_id  = isset($_REQUEST['SummitID']) ?  $_REQUEST['SummitID'] :  $this->Room()->Venue()->SummitIDD;
        $summit     = Summit::get()->byID($summit_id);
        if(is_null($summit)) throw new InvalidArgumentException('summit not found!');
        $value = $this->getField('EndDateTime');
        return $summit->convertDateFromUTC2TimeZone($value);
    }

    /**
     * @return string
     */
    public function getEndDateTimeUTC(){
        return  $this->getField('EndDateTime');
    }

    public function getCMSFields()
    {
        $f = new FieldList
        (
            $rootTab = new TabSet("Root", $tabMain = new Tab('Main'))
        );

        $f->addFieldToTab('Root.Main', $date = new DatetimeField('StartDateTime', "When does booking begins?"));
        $date->getDateField()->setConfig('showcalendar', true);
        $date->getDateField()->setConfig('dateformat', 'dd/MM/yyyy');

        $f->addFieldToTab('Root.Main', $date = new DatetimeField('EndDateTime', "When does booking ends?"));
        $date->getDateField()->setConfig('showcalendar', true);
        $date->getDateField()->setConfig('dateformat', 'dd/MM/yyyy');


        $f->addFieldToTab('Root.Main', $ddl_status = new DropdownField('Status','Status', [
            "Reserved"        => "Reserved",
            "Payed"           => "Payed",
            "RequestedRefund" => "RequestedRefund",
            "Refunded"        => "Refunded",
            "Canceled"        => "Canceled"
        ]));

        $ddl_status->setEmptyString("-- SELECT A STATUS --");

        $f->addFieldToTab('Root.Main', new HiddenField('RoomID','RoomID'));
        $f->addFieldToTab('Root.Main', new TextField('OwnerID', 'Owner ID'));
        $f->addFieldToTab('Root.Main', new NumericField('Amount', 'Amount'));
        $f->addFieldToTab('Root.Main', new NumericField('RefundedAmount', 'RefundedAmount'));
        $f->addFieldToTab('Root.Main', $ddl_currency = new DropdownField('Currency','Currency (ISO 4217)', [
            "USD" => "USD",
            "EUR" => "EUR",
            "GBP" => "GBP",
        ]));

        $ddl_currency->setEmptyString("-- SELECT A CURRENCY --");

        $f->addFieldToTab('Root.Main', $date = new DatetimeField('ApprovedPaymentDate', "ApprovedPaymentDate"));
        $date->getDateField()->setConfig('showcalendar', true);
        $date->getDateField()->setConfig('dateformat', 'dd/MM/yyyy');

        return $f;
    }
}