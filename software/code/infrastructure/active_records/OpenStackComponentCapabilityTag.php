<?php
/**
 * Copyright 2019 Openstack Foundation
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
 * Class OpenStackComponentCapabilityTag
 */
class OpenStackComponentCapabilityTag extends DataObject implements IOpenStackComponentCapabilityTag
{

    static $db = array
    (
        'Name'         => 'Varchar(255)',
        'Description'  => 'Text',
        'Enabled'      => 'Boolean(1)'
    );

    static $has_one = array
    (
        'Category'  => 'OpenStackComponentCapabilityCategory'
    );

    /**
     * @return int
     */
    public function getIdentifier()
    {
        return (int)$this->getField('ID');
    }


    public function getColor() {
        return $this->Category()->getColor();
    }

    public function CategoryName() {
        return $this->Category()->Name;
    }

}