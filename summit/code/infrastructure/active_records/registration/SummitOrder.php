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

class SummitOrder extends DataObject
{
    private static $db = [
        'Number' => 'Varchar(255)',
        'ExternalId' => 'Varchar(255)',
        'PaymentMethod' => "Enum('Online,Offline','Offline')",
        'Status' => "Enum('Reserved,Cancelled,RefundRequested,Refunded,Confirmed,Paid','Reserved')",
        'OwnerFirstName' => 'Varchar(255)',
        'OwnerSurname' => 'Varchar(255)',
        'OwnerEmail' => 'Varchar(100)',
        'OwnerCompany' => 'Varchar(255)',
        'BillingAddress1' => 'Varchar(100)',
        'BillingAddress2' => 'Varchar(100)',
        'BillingAddressZipCode' => 'Varchar(50)',
        'BillingAddressCity' => 'Varchar(50)',
        'BillingAddressState' => 'Varchar(50)',
        'BillingAddressCountryISOCode' => 'Varchar(3)',
        'ApprovedPaymentDate' => 'SS_Datetime',
        'LastError' => 'Varchar(255)',
        'PaymentGatewayCartId' => 'VarChar(512)',
        'PaymentGatewayClientToken' => 'Text',
        'QRCode' => 'Varchar(255)',
        'Hash' => 'Varchar(255)',
        'HashCreationDate' => 'SS_Datetime',
        'RefundedAmount' => 'Currency',
    ];

    private static $has_many = [
        'Tickets' => 'SummitAttendeeTicket',
        'Answers' => 'SummitOrderExtraQuestionAnswer',
    ];

    private static $has_one = [
        'Summit' => 'Summit',
        'Owner' => 'Member',
        'Company' => 'Company',
    ];

    private static $summary_fields = [
        'Number' => 'Number',
        'PaymentMethod' => 'Payment Method',
        "OwnerFullName" => "Owner",
    ];

    private static $many_many = [
    ];

    /**
     * @return string
     */
    public function getOwnerFullName(): string
    {
        if ($this->Owner()->exists()) {
            return $this->Owner()->getFullName();
        }
        return sprintf("%s, %s", $this->OwnerFirstName, $this->OwnerSurname);
    }

    // CMS admin UI
    public function getCMSFields()
    {

        $summit_id = isset($_REQUEST['SummitID']) ? $_REQUEST['SummitID'] : $this->SummitID;

        $f = FieldList::create(TabSet::create('Root'));

        $f->addFieldsToTab("Root.Main",
            [
                new HiddenField('SummitID', 'SummitID'),
                new TextField('Number', 'Number'),
                new TextField('ExternalId', 'External Id'),
                new TextField('OwnerFirstName', 'OwnerFirstName'),
                new TextField('OwnerSurname', 'OwnerSurname'),
                new TextField('OwnerEmail', 'OwnerEmail'),
                new TextField('OwnerCompany', 'OwnerCompany'),
                new TextField('QRCode', 'QRCode'),
                new DropdownField('PaymentMethod', 'Payment Method', SummitOrder::create()->dbObject('PaymentMethod')->enumValues()),
                new DropdownField('Status', 'Status', SummitOrder::create()->dbObject('Status')->enumValues()),
            ]);

        $f->addFieldsToTab("Root.Billing Address",
            [
                new TextField('BillingAddress1', 'BillingAddress1'),
                new TextField('BillingAddress2', 'BillingAddress2'),
                new TextField('BillingAddressZipCode', 'BillingAddressZipCode'),
                new TextField('BillingAddressCity', 'BillingAddressCity'),
                new TextField('BillingAddressState', 'BillingAddressState'),
                new TextField('BillingAddressCountryISOCode', 'BillingAddressCountryISOCode'),

            ]);

        if($this->ID > 0) {
            $config = GridFieldConfig_RelationEditor::create(50);
            $tickets = new GridField('Tickets', 'Tickets', $this->Tickets(), $config);
            $f->addFieldToTab('Root.Tickets', $tickets);

            $config = GridFieldConfig_RelationEditor::create(50);
            $answers = new GridField('Answers', 'Answers', $this->Answers(), $config);
            $f->addFieldToTab('Root.Answers', $answers);
        }

        return $f;

    }
}