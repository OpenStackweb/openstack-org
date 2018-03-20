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

class PageSectionVideos extends PageSection {

	static $db = array();

	static $many_many = array(
	    'Videos' => 'VideoLink'
    );

    private static $many_many_extraFields = array(
        'Videos' => array(
            'Order' => 'Int',
        )
    );

    function getCMSFields() {
        $fields = parent::getCMSFields();

        $config = GridFieldConfig_RecordEditor::create(4);
        $config->addComponent(new GridFieldSortableRows('Order'));
        $gridField = new BetterGridField('Videos', 'Videos', $this->Videos(), $config);

        if ($this->ID) {
            $fields->add($gridField);
        }

        return $fields;
    }
}