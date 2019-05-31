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

class SummitBookableVenueRoom extends SummitVenueRoom
{
    private static $db = [

        'TimeSlotCost'  => 'Currency',
        'Currency'      => 'VarChar(3)',
    ];

    private static $has_many = [
        'Reservations' => 'SummitRoomReservation'
    ];

    /**
     * @var array
     */
    private static $many_many = [
        'Attributes' => 'SummitBookableVenueRoomAttributeValue',
    ];

    public function getCMSFields()
    {
        $f = parent::getCMSFields();
        $f->addFieldToTab('Root.Main', new CurrencyField('TimeSlotCost','Time Slot Cost'));

        $f->addFieldToTab('Root.Main', $ddl_currency = new DropdownField('Currency','Currency (ISO 4217)', [
            "USD" => "USD",
            "EUR" => "EUR",
            "GBP" => "GBP",
        ]));

        $ddl_currency->setEmptyString("-- SELECT A CURRENCY --");

        $config = GridFieldConfig_RecordEditor::create();
        $gridField = new GridField('Reservations', 'Reservations', $this->Reservations(), $config);
        $f->addFieldToTab('Root.Reservations', $gridField);

        $config = GridFieldConfig_RelationEditor::create();
        $gridField = new GridField('Attributes', 'Attributes', $this->Attributes(), $config);
        $f->addFieldToTab('Root.Attributes', $gridField);
        return $f;
    }

}