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

class SummitRegistrationDiscountCode extends SummitRegistrationPromoCode
{
    private static $db = [
        'DiscountRate'     => 'Currency',
        'DiscountAmount'   => 'Currency',
    ];

    private static $many_many = [
        'AllowedTicketTypes' => 'SummitTicketType',
    ];

    // discount rules per tix types
    private static $many_many_extraFields = [
        'AllowedTicketTypes' => [
            'DiscountRate'     => "Currency",
            'DiscountAmount'   => 'Currency',
        ],
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

        $f->addFieldToTab("Root.Main", new NumericField("DiscountRate","DiscountRate"));
        $f->addFieldToTab("Root.Main", new NumericField("DiscountAmount","DiscountAmount"));


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

            $f->removeFieldFromTab("Root.Allowed Ticket Types", "AllowedTicketTypes");
            $config = GridFieldConfig_RelationEditor::create(25);
            $config->removeComponentsByType(new GridFieldDataColumns());
            $config->removeComponentsByType(new GridFieldDetailForm());
            $auto_completer = $config->getComponentByType('GridFieldAddExistingAutocompleter');
            $auto_completer->setSearchList(SummitTicketType::get()->filter('SummitID', $summit_id));
            $auto_completer->setResultsFormat('$Name ($ID)');
            $editconf = new GridFieldDetailForm();
            $editconf->setFields(FieldList::create(
                ReadonlyField::create('SummitID','Summit'),
                ReadonlyField::create('Name','Name'),
                NumericField::create('ManyMany[DiscountRate]', 'Discount Rate ( Number between 0.00 and 100.00 )'),
                CurrencyField::create('ManyMany[DiscountAmount]', 'Discount Amount ')
            ));

            $summaryfieldsconf = new GridFieldDataColumns();
            $summaryfieldsconf->setDisplayFields([
                'Name' => 'Ticket Type Name',
                'DiscountRate' => 'Discount tate',
                'DiscountAmount' => 'Discount Amount'
            ]);

            $config->addComponent($editconf);
            $config->addComponent($summaryfieldsconf);
            $ticket_types = new GridField('AllowedTicketTypes', 'Allowed Ticket Types', $this->AllowedTicketTypes(), $config);
            $f->addFieldToTab('Root.Allowed Ticket Types', $ticket_types);

        }
        return $f;

    }

    public function manyManyComponent($component) {
        $res = parent::manyManyComponent($component);
        if($component == "AllowedTicketTypes"){
            // remapping
            $res[2] = 'SummitRegistrationDiscountCodeID';
            $res[4] = 'SummitRegistrationDiscountCode_AllowedTicketTypes';
        }
        return $res;
    }

}