<?php
/**
 * Copyright 2016 Open Infrastructure Foundation
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

interface IGerritUserRepository extends IMemberRepository
{
    /**
     * @param int $page_nbr
     * @param int $page_size
     * @return array
     */
    function getAllGerritUsersByPage($page_nbr = 1, $page_size = 100);

    /**
     * @param string $account_id
     * @return GerritUser
     */
    function getGerritUserByAccountId($account_id);
}