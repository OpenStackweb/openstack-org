<?php

/**
 * Copyright 2015 OpenStack Foundation
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
interface ISoftwareManager
{
    /**
     * @param IOpenStackRelease $release
     * @param string $term
     * @param int $adoption
     * @param int $maturity
     * @param int $age
     * @param string $sort
     * @param string $sort_dir
     * @return array
     */
    public function getComponents(IOpenStackRelease $release , $term = '', $adoption = 0, $maturity = 0, $age = 0, $sort = '', $sort_dir = '');

    /**
     * @param IOpenStackRelease $release
     * @return IOpenStackRelease
     */
    public function cloneRelease(IOpenStackRelease $release);
}