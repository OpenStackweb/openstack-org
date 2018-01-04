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
 * Class OpenStackComponentCategory
 */
class OpenStackComponentCategory extends DataObject implements IOpenStackComponentCategory
{

    static $db = array
    (
        'Name'              => 'Varchar(255)',
        'Description'       => 'Text',
        'Order'             => 'Int'
    );

    static $many_many = array
    (
        'SubCategories' => 'OpenStackComponentSubCategory'
    );

    static $many_many_extraFields = array(
        'SubCategories' => array(
            'SubCatOrder' => 'Int'
        )
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
    public function getSubCategories()
    {
        return AssociationFactory::getInstance()->getMany2ManyAssociation($this, 'SubCategories')->toArray();
    }

    /**
     * @param IOpenStackComponentSubCategory $new_sub_category
     * @throws Exception
     */
    public function addSubCategory(IOpenStackComponentSubCategory $new_sub_category)
    {
        AssociationFactory::getInstance()->getMany2ManyAssociation($this, 'SubCategories')->add($new_sub_category);
    }

    /**
     * @param int $component_id
     * @return bool
     */
    public function hasOpenStackComponent($component_id)
    {
        foreach ($this->getSubCategories() as $subCategory) {
            if ($subCategory->hasOpenStackComponent($component_id)) {
                return true;
            }
        }

        return false;
    }

}