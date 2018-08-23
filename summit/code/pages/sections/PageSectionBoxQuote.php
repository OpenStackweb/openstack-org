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

class PageSectionBoxQuote extends PageSectionBox {

    static $has_one = array(
        'Speaker' => 'PresentationSpeaker'
    );

    function getCMSFields() {
        $fields = parent::getCMSFields();

        $config = GridFieldConfig_RelationEditor::create(1);
        $auto_completer = $config->getComponentByType('GridFieldAddExistingAutocompleter');
        $auto_completer->setResultsFormat('$FirstName $LastName');
        $config->getComponentByType('GridFieldDataColumns')->setDisplayFields(
            [
                'FirstName' => 'FirstName',
                'LastName'  => 'LastName',
                'Title'     => 'Title',
            ]);
        $gridField = new BetterGridField('Speaker', 'Speaker', $this->Speaker(), $config);

        if ($this->ID) {
            $fields->add($gridField);
        }

        return $fields;
    }
}