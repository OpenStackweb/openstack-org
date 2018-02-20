<?php
/**
 * Copyright 2017 Openstack Foundation
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
 * Class OpenStackComponentSubCategory
 */
class OpenStackComponentSubCategory extends DataObject implements IOpenStackComponentSubCategory
{

    static $db = array
    (
        'Name'              => 'Varchar(255)',
        'Description'       => 'Text'
    );

    static $has_many = array
    (
        'OpenStackComponents' => 'OpenStackComponent'
    );

    static $belongs_many_many = array
    (
        'Categories' => 'OpenStackComponentCategory'
    );

    /**
     * @return int
     */
    public function getIdentifier()
    {
        return (int)$this->getField('ID');
    }

    public function getName()
    {
        return $this->getField('Name');
    }

    public function setName($name)
    {
        $this->setField('Name', $name);
    }

    public function getDescription()
    {
        return $this->getField('Description');
    }

    public function setDescription($description)
    {
        $this->setField('Description', $description);
    }

    public function getOrder()
    {
        return $this->getField('Order');
    }

    public function setOrder($order)
    {
        $this->setField('Order', $order);
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getOpenStackComponents()
    {
        return AssociationFactory::getInstance()->getOne2ManyAssociation($this, 'OpenStackComponents')->toArray();
    }

    /**
     * @param IOpenStackComponent $new_component
     * @throws Exception
     */
    public function addOpenStackComponent(IOpenStackComponent $new_component)
    {
        AssociationFactory::getInstance()->getOne2ManyAssociation($this, 'OpenStackComponents')->add($new_component);
    }

    /**
     * @param int $component_id
     * @return bool
     */
    public function hasOpenStackComponent($component_id)
    {
        foreach ($this->getOpenStackComponents() as $component) {
            if ($component->getIdentifier() == $component_id) {
                return true;
            }
        }

        return false;
    }

}