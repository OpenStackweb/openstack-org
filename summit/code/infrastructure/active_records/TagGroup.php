<?php
/**
 * Copyright 2014 Openstack Foundation
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
 * Class TagGroup
 */
final class TagGroup extends DataObject implements ITagGroup
{

    static $create_table_options = array('MySQLDatabase' => 'ENGINE=InnoDB');

    static $db = array
    (
        'Name'      => 'Varchar',
        'Label'     => 'Varchar',
        'Order'     => 'Int',
        'Mandatory' => 'Boolean(0)'
    );

    /**
     * @return int
     */
    public function getIdentifier()
    {
        return (int)$this->getField('ID');
    }

    static function getGroups() {
        return TagGroup::get()->sort('Order')->map('Name', 'Label')->toArray();
    }


    public function getCMSFields() {
        $fields = new FieldList();
        $fields->add(new LiteralField('namelabel', 'Name is the label used in CFP, use only lowercase'));
        $fields->add(new TextField('Name', 'Name (lowercase)'));
        $fields->add(new TextField('Label'));

        return $fields;
    }
}