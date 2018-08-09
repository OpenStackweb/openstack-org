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
 * Interface IOpenStackComponentCategory
 */
interface IOpenStackComponentCategory extends IEntity
{

    public function getName();

    public function setName($name);

    public function getDescription();

    public function setDescription($description);

    public function getOrder();

    public function setOrder($order);

    /**
     * @return array
     * @throws Exception
     */
    public function getSubCategories();

    /**
     * @param IOpenStackComponentCategory $new_sub_category
     * @throws Exception
     */
    public function addSubCategory(IOpenStackComponentCategory $new_sub_category);

    /**
     * @param int $component_id
     * @return bool
     */
    public function hasOpenStackComponent($component_id);

} 