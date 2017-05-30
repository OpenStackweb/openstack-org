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
class SponsorshipType extends DataObject implements ISponsorshipType
{

    private static $create_table_options = array('MySQLDatabase' => 'ENGINE=MyISAM');

    static $db = array
    (
        'Name'  => 'Varchar',
        'Label' => 'Varchar',
        'Order' => 'Int',
        'Size'  => "Enum('Small, Medium, Large, Big', 'Medium')",
    );

    /**
     * @return int
     */
    public function getIdentifier()
    {
        return (int)$this->getField('ID');
    }

    public function getSizeClass()
    {
        switch ($this->Size) {
            case 'Small':
                return 'col-lg-1 col-md-1 col-sm-1';
                break;
            case 'Medium':
                return 'col-lg-2 col-md-2 col-sm-2';
                break;
            case 'Large':
                return 'col-lg-3 col-md-3 col-sm-3';
                break;
            case 'Big':
                return 'col-lg-4 col-md-4 col-sm-4';
                break;
        }
    }
}