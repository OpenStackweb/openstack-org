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
        'Label'             => 'Varchar(255)',
        'Description'       => 'Text',
        'Slug'              => 'Varchar(255)',
        'Order'             => 'Int',
        'Enabled'           => 'Boolean(1)'
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

        if (empty($this->Slug)) {
            $slug = strtolower($this->Name);
            $slug = preg_replace("/[^a-z0-9_\s-]/", "", $slug);
            $slug = preg_replace("/[\s-]+/", " ", $slug);
            $slug = preg_replace("/[\s_]/", "-", $slug);

            $checkSlug = $slug;
            $counter = 0;
            while ($slugExists = OpenStackComponentCategory::get()->exclude('ID', $this->ID)->filter('Slug', $checkSlug)->count()) {
                $counter++;
                $checkSlug = $slug . '-' . $counter;
            }

            $this->Slug = $checkSlug;
        }

        if (empty($this->Label)) {
            $this->Label = $this->Name;
        }

        // if removed from a collection of subcategories we disable the category
        if (!$this->ParentCategoryID && $this->isChanged('ParentCategoryID')) {
            $this->Enabled = 0;
        }
    }

    protected function validate() {
        $valid = parent::validate();
        if(!$valid->valid()) return $valid;

        $parentIds = $this->getAllParentIds();

        // DO NOT ALLOW A CLOSED LOOP
        if (in_array($this->ID, $parentIds)) {
            return $valid->error('Loop relation! Category belongs to the same category');
        }

        return $valid;
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
        return AssociationFactory::getInstance()
            ->getOne2ManyAssociation($this, 'SubCategories')->toArray();
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

    /**
     * @return DataList
     */
    public function getActiveSubCategories()
    {
        return $this->SubCategories()->filter('Enabled', 1)->sort('Order');
    }

    /**
     * @return DataList
     */
    public function getSiblings()
    {
        if ($this->ParentCategoryID != 0) {
            $siblings =  OpenStackComponentCategory::get()
                ->filter(['Enabled' => 1, 'ParentCategoryID' => $this->ParentCategoryID])
                ->sort('Order');

            if ($this->ID) {
                $siblings = $siblings->exclude('ID', $this->ID);
            }

            return $siblings;
        }

        return new ArrayList();
    }

    public static function getFilteredCategoryMap($categories, $componentIds, $serializer) {
        $map = [];

        foreach($categories as $category) {
            $components = [];
            $subcategories = [];

            if ($category->getActiveSubCategories()->count() > 0) {
                $subcategories = OpenStackComponentCategory::getFilteredCategoryMap($category->getActiveSubCategories(), $componentIds, $serializer);
            } else if ($category->OpenStackComponents()->count() > 0) {
                foreach ($category->OpenStackComponents()->filter('ID', $componentIds)->sort('Order') as $component) {
                    $components[] = $serializer->serialize($component);
                }
            }

            // clean-up
            if (!count($components) && !count($subcategories)) {
                continue;
            }

            $catKey = $category->Slug;
            $map[$catKey] = [];
            $map[$catKey]['category'] = $category->toMap();

            if (count($components)) {
                $map[$catKey]['components'] = $components;
            }

            if (count($subcategories)) {
                $map[$catKey]['subcategories'] = $subcategories;
            }
        }

        return $map;
    }

    public function getParentCategory() {
        if ($this->ParentCategory()->Exists()) {
            return $this->ParentCategory()->getParentCategory();
        } else {
            return $this;
        }
    }

    public function getAllParentIds() {
        $ids = [];
        $parentCat = $this->ParentCategory();

        while($parentCat->Exists()) {
            $ids[] = $parentCat->ID;
            $parentCat = $parentCat->ParentCategory();
        }

        return $ids;
    }

    public function getDepth() {
        $subCats = $this->getActiveSubCategories();

        if ($subCats->count() > 0) {
            $maxDepth = 0;
            foreach ($subCats as $subCat) {
                $maxDepth = max($maxDepth, $subCat->getDepth());
            }
            return $maxDepth + 1;
        } else {
            return 1;
        }
    }

    public static function getParentCategories() {
        return OpenStackComponentCategory::get()->filter(['ParentCategoryID' => 0, 'Enabled' => 1])->sort('Order');
    }

}