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
 * Interface ISecurityGroup
 */
interface ISecurityGroup extends IEntity
{
    /**
     * @return string
     */
    public function getTitle();

    /**
     * @param string $title
     * @return void
     */
    public function setTitle($title);

    /**
     * @return string
     */
    public function getSlug();

    /**
     * @param string $slug
     * @return void
     */
    public function setSlug($slug);

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @param string $description
     * @return void
     */
    public function setDescription($description);
} 