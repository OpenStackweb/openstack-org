<?php
/**
 * Copyright 2018 Openstack Foundation
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

class PageSectionSponsors extends PageSectionText {

	static $db = array();

	static $many_many = array(
	    'Sponsors' => 'Company'
    );

    private static $many_many_extraFields = array(
        'Sponsors' => array(
            'Order' => 'Int',
        )
    );

    function getCMSFields() {
        $fields = parent::getCMSFields();

        //die('summ: '.$this->owner->Summit()->ID);

        $config = GridFieldConfig_RelationEditor::create(8);
        $config->addComponent(new GridFieldSortableRows('Order'));
        $config->removeComponentsByType('GridFieldAddNewButton');
        /*$auto_completer = $config->getComponentByType('GridFieldAddExistingAutocompleter');
        $auto_completer->setResultsFormat('$Company.Name');
        $config->getComponentByType('GridFieldAddExistingAutocompleter')
            ->setSearchList(Sponsor::get()->filter('SummitID',$this->owner->SummitID));
        $config->getComponentByType('GridFieldDataColumns')
            ->setDisplayFields(
                [
                    'Name' => 'Company.Name',
                    'Link' => 'Company.URL'
                ]
        );*/

        $gridField = new BetterGridField('Sponsors', 'Sponsors', $this->Sponsors(), $config);

        if ($this->ID) {
            $fields->add($gridField);
        }

        return $fields;
    }
}