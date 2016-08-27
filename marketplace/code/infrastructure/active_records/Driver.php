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
 * Class Driver
 */
class Driver extends DataObject implements IDriver
{

    static $create_table_options = array('MySQLDatabase' => 'ENGINE=InnoDB');

    static $db = array(
        'Name' => 'Varchar(255)',
        'Description' => 'HTMLText',
        'Project' => 'Varchar(255)',
        'Vendor' => 'Varchar(255)',
        'Url' => 'Varchar(255)',
        'Tested' => 'Boolean',
        'Active' => 'Boolean'
    );

    static $many_many = array(
        'Releases' => 'DriverRelease',
    );


    static $indexes = array(
        'Name_Project' => array('type' => 'unique', 'value' => 'Name, Project')
    );

    static $summary_fields = array(
        'Name' => 'Name',
        'Description' => 'Description',
        'Project' => 'Project',
        'Vendor' => 'Vendor',
        'Tested' => 'Tested'
    );

    /**
     * @return int
     */
    public function getIdentifier()
    {
        return (int)$this->getField('ID');
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->getField('Name');
    }

    /**
     * @param string $name
     * @return void
     */
    public function setName($name)
    {
        $this->setField('Name', $name);
    }

    public function getReleases() {
        return $this->Releases()->filter('Active',1);
    }

}