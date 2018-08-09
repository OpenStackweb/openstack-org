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
        'Slug'              => 'Varchar(255)',
        'Order'             => 'Int'
    );

    static $has_many = array
    (
        'OpenStackComponents'   => 'OpenStackComponent',
        'SubCategories'         => 'OpenStackComponentCategory'
    );

    static $has_one = array
    (
        "ParentCategory"        => "OpenStackComponentCategory"
    );

    protected function onBeforeWrite()
    {
        parent::onBeforeWrite();
        if ($this->Order == 0) {
            $siblings_count = $this->getSiblings()->count();
            $this->Order = $siblings_count + 1;
        }

        $slug = strtolower($this->Name);
        $slug = preg_replace("/[^a-z0-9_\s-]/", "", $slug);
        $slug = preg_replace("/[\s-]+/", " ", $slug);
        $slug = preg_replace("/[\s_]/", "-", $slug);

        $this->Slug = $slug;
    }

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
        return AssociationFactory::getInstance()->getOne2ManyAssociation($this, 'SubCategories')->toArray();
    }

    /**
     * @param IOpenStackComponentCategory $new_sub_category
     * @throws Exception
     */
    public function addSubCategory(IOpenStackComponentCategory $new_sub_category)
    {
        AssociationFactory::getInstance()->getOne2ManyAssociation($this, 'SubCategories')->add($new_sub_category);
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
        foreach ($this->getSubCategories() as $subCategory) {
            if ($subCategory->hasOpenStackComponent($component_id)) {
                return true;
            }
        }

        return false;
    }

    public function getSiblings()
    {
        if ($this->ParentCategoryID != 0) {
            $siblings =  OpenStackComponentCategory::get()
                ->filter('ParentCategoryID', $this->ParentCategoryID);

            if ($this->ID) {
                $siblings = $siblings->exclude('ID', $this->ID);
            }

            return $siblings;
        }

        return [];
    }

    public static function getFilteredCategoryMap($categories, $componentIds, $serializer) {
        $map = [];

        foreach($categories as $category) {
            $components = [];
            $subcategories = [];

            if ($category->SubCategories()->count() > 0) {
                $subcategories = OpenStackComponentCategory::getFilteredCategoryMap($category->SubCategories(), $componentIds, $serializer);
            } else if ($category->OpenStackComponents()->count() > 0) {
                foreach ($category->OpenStackComponents()->filter('ID', $componentIds) as $component) {
                    $components[] = $serializer->serialize($component);
                }
            }

            // clean-up
            if (!count($components) && !count($subcategories)) {
                continue;
            }

            $map[$category->ID] = [];
            $map[$category->ID]['category'] = $category->toMap();

            if (count($components)) {
                $map[$category->ID]['components'] = $components;
            }

            $depth = 1;
            if (count($subcategories)) {
                $map[$category->ID]['subcategories'] = $subcategories;
                $depth = array_values($subcategories)[0]['depth'] + 1;
            }

            $map[$category->ID]['depth'] = $depth;
        }

        return $map;
    }

    public static function getParentCategories() {
        return OpenStackComponentCategory::get()->filter('ParentCategoryID', 0);
    }

}