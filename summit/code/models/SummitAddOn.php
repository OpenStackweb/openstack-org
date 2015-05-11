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
 * Class SummitAddOn
 */
class SummitAddOn extends DataObject implements ISummitAddOn
{

    private static $db = array (
        'Title'              => 'Text',
        'Cost'               => 'Currency',
        'MaxAvailable'       => 'Int',
        'CurrentlyAvailable' => 'Int',
        'Order'              => 'Int',
        'ShowQuantity'       => 'Boolean'
    );

    private static $defaults = array(
        'ShowQuantity' => TRUE
    );    
    
    private static $has_one = array (
        'SummitSponsorPage' => 'SummitSponsorPage'
    );
    
    private static $summary_fields = array(
        'Title',
        'Cost',
        'MaxAvailable',
        'CurrentlyAvailable'
    );
    
    public function getCMSFields() {
        $fields = new FieldList();
        $fields->add(new TextField('Title','Title'));
        $fields->add(new CheckboxField('ShowQuantity','Show Quantities'));
        $fields->add(new CurrencyField('Cost','Cost'));
        $fields->add(new NumericField('MaxAvailable','Max. Available'));
        $fields->add(new NumericField('CurrentlyAvailable','Currently Available'));
        return $fields;
    } 
    
    public function SoldOut() {
        return $this->CurrentlyAvailable == 0;
    }

    /**
     * @return int
     */
    public function getIdentifier()
    {
        return (int)$this->getField('ID');
    }
}