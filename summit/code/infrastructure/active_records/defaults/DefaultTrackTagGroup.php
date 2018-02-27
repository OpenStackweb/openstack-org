<?php
/**
 * Copyright 2018 OpenStack Foundation
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
 * Class DefaultTrackTagGroup
 */
final class DefaultTrackTagGroup extends DataObject
{
    static $db = [
        'Name'      => 'Varchar',
        'Label'     => 'Varchar',
        'Order'     => 'Int',
        'Mandatory' => 'Boolean(0)'
    ];

    private static $has_one = [
    ];

    private static $many_many = [
        'AllowedTags' => 'Tag'
    ];

    public function getCMSFields() {
        $fields = new FieldList();

        $fields->add(new LiteralField('namelabel', 'Name is the label used in CFP, use only lowercase'));
        $fields->add(new TextField('Name', 'Name (lowercase)'));
        $fields->add(new TextField('Label'));
        if($this->ID > 0) {
            $fields->tag('AllowedTags', 'Allowed Tags', Tag::get(), $this->AllowedTags())
                ->configure()
                ->setTitleField('Tag')
                ->end();
        }
        return $fields;
    }
}