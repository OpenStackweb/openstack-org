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
final class SummitAttendeeTicket extends DataObject implements ISummitAttendeeTicket
{
    private static $db = [

        // https://www.eventbrite.com/developer/v3/formats/order/#ebapi-std:format-order
        'Status'              => "Enum('Reserved,Cancelled,RefundRequested,Refunded,Confirmed,Paid','Reserved')",
        'ExternalOrderId'     => 'Varchar(255)',
        'ExternalAttendeeId'  => 'Varchar(255)',
        'TicketBoughtDate'    => 'SS_Datetime',
        'TicketChangedDate'   => 'SS_Datetime',
        'Number'              => 'Varchar(255)',
        'RawCost'             => 'Currency',
        'Discount'            => 'Currency',
        'RefundedAmount'      => 'Currency',
        'Currency'            => 'VarChar(3)',
        'QRCode'              => 'Varchar(255)',
        'Hash'                => 'Varchar(255)',
        'HashCreationDate'    => 'SS_Datetime',
    ];

    private static $has_one = [
        'TicketType'          => 'SummitTicketType',
        'SummitAttendeeBadge' => 'SummitAttendeeBadge',
        'Owner'               => 'SummitAttendee',
        'Order'               => 'SummitOrder',
        'PromoCode'           => 'SummitRegistrationPromoCode',
    ];

    private static $summary_fields = array
    (
        "TicketBoughtDate"        => 'Bought Date',
        'ExternalOrderId'         => "#Order",
        'ExternalAttendeeId'      => "#Attendee",
        'OwnerFullName'           => "Attendee",
        'TicketType.Name'         => 'Ticket Type',
        'PromoCodeNice'           => 'Promo Code',
    );

    private static $many_many = [
        'Taxes' => 'SummitTaxType',
    ];

    // discount rules per tix types
    private static $many_many_extraFields = [
        'Taxes' => [
            'Amount' => "Currency",
        ],
    ];

    public function getCMSFields()
    {

        $summit_id = isset($_REQUEST['SummitID']) ? intval($_REQUEST['SummitID']) : 0;

        if(empty($summit_id))
        {
            $summit_id =  $this->Owner()->exists() ? $this->Owner()->SummitID : Summit::get_active()->ID;
        }


        $f = new FieldList
        (
            $rootTab = new TabSet("Root", $tabMain = new Tab('Main'))
        );

        $f->addFieldToTab('Root.Main', new HiddenField('OwnerID','OwnerID'));
        $f->addFieldToTab('Root.Main', new ReadonlyField('Number','Number'));
        $f->addFieldToTab('Root.Main', new ReadonlyField('RawCost','Cost'));
        $f->addFieldToTab('Root.Main', new ReadonlyField('Discount','Discount'));

        $f->addFieldToTab('Root.Main', new ReadonlyField('OwnerFullName','Owner'));

        $f->addFieldToTab('Root.Main', new ReadonlyField('RefundedAmount','RefundedAmount'));
        $f->addFieldToTab('Root.Main', new ReadonlyField('PromoCodeNice','Promo Code'));
        $f->addFieldToTab('Root.Main', new TextField('ExternalOrderId', '# External Order ID'));
        $f->addFieldToTab('Root.Main', new TextField('ExternalAttendeeId', '# External Attendee'));

        $f->addFieldToTab('Root.Main', $date = new DatetimeField('TicketBoughtDate', 'Bought Date'));
        $date->getDateField()->setConfig('showcalendar', true);

        $f->addFieldToTab('Root.Main', $date = new DatetimeField('TicketChangedDate', 'Changed Date'));
        $date->getDateField()->setConfig('showcalendar', true);

        $f->addFieldToTab('Root.Main', $ddl = new DropdownField('TicketTypeID', 'Ticket Type', SummitTicketType::get()->filter('SummitID', $summit_id)->map("ID","Name")));
        return $f;
    }

    function getOwnerFullName():string{
        if($this->OwnerID > 0)
            return $this->Owner()->getFullName();
        return 'TBD';
    }

    function getPromoCodeNice():string{
        if($this->PromoCodeID > 0)
            return $this->PromoCode()->Code;
        return 'NA';
    }

    function getCMSValidator()
    {
        return $this->getValidator();
    }

    function getValidator()
    {
        $validator= new RequiredFields(array('ExternalOrderId', 'ExternalAttendeeId', 'TicketTypeID'));
        return $validator;
    }

    static $indexes = array
    (
        'Order_Attendee' =>  array('type'=>'index', 'value'=>'ExternalOrderId,ExternalAttendeeId')
    );

    /**
     * @return int
     */
    public function getIdentifier()
    {
        return (int)$this->getField('ID');
    }
}