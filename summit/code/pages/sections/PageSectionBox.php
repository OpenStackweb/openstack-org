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

class PageSectionBox extends DataObject {

    static $db = array(
        'Title'         => 'Varchar(255)',
        'Text'          => 'HTMLText',
        'ButtonLink'    => 'Varchar(255)',
        'ButtonText'    => 'Varchar(100)',
        'Size'          => 'Int',
        'Order'         => 'Int'
    );

    static $has_one = array(
        'ParentSection' => 'PageSectionBoxes'
    );

    function getCMSFields() {

        $fields = new FieldList (
            new TextField('Title','Title:'),
            new HtmlEditorField('Text','Text:'),
            new TextField ('ButtonLink','Link:'),
            new TextField ('ButtonText','Label:'),
            new TextField ('Size','Size (1 or 2):'),
            new HiddenField('ParentSectionID','ParentSectionID')
        );

        return $fields;
    }

    function isClass($className) {
        return $this->is_a($className);
    }
}