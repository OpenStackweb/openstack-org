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

class PageSection extends DataObject {

    static $db = array(
        'Name'          => 'Varchar(100)',
        'Title'         => 'Varchar(255)',
        'IconClass'     => 'Varchar(50)',
        'WrapperClass'  => 'Varchar(100)',
        'ShowInNav'     => 'Boolean',
        'Enabled'       => 'Boolean(1)',
    );

    static $singular_name = 'Section';
    static $plural_name = 'Sections';

    static $summary_fields = array(
        'Name'  => 'Name',
        'Title' => 'Title',
        'Enabled' => 'Enabled'
    );

    function getCMSFields() {
        $fields = new FieldList (
            new TextField('Name','Name this section (no spaces):'),
            new TextField('Title','Title:'),
            new TextField ('IconClass','Fontawesome class for the label icon (optional)'),
            new TextField ('WrapperClass','Class for the wrapper div (optional)'),
            new CheckboxField('ShowInNav', 'Show in Navigation'),
            new CheckboxField('Enabled', 'Enabled')
        );

        return $fields;
    }

    function isClass($className) {
        return $this->is_a($className);
    }
}