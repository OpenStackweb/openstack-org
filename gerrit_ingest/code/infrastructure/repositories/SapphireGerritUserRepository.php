<?php

/**
 * Copyright 2016 OpenStack Foundation
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
 * Class SapphireGerritUserRepository
 */
final class SapphireGerritUserRepository extends SapphireMemberRepository implements IGerritUserRepository
{

    /**
     * SapphireGerritUserRepository constructor.
     */
    public function __construct()
    {
        $entity = new GerritUser();
        parent::__construct($entity);
    }

    /**
     * @param string $account_id
     * @return GerritUser
     */
    function getGerritUserByAccountId($account_id)
    {
        return GerritUser::get()->filter('AccountID', $account_id)->first();
    }

    /**
     * @param int $page_nbr
     * @param int $page_size
     * @return GerritUser[]
     */
    function getAllGerritUsersByPage($page_nbr = 1, $page_size = 100)
    {

        $offset      = ($page_nbr -1 ) * $page_size;

        $query_count = DB::query("SELECT COUNT(ID) AS QTY FROM GerritUser;");
        $total = intval($query_count->column('QTY')[0]);
        $query_select = DB::query("SELECT GerritUser.* FROM GerritUser LIMIT {$page_size} OFFSET {$offset};");

        $res = [];

        foreach ($query_select as $row){
            $res[] = new GerritUser($row);
        }

        return [$total, $res];
    }
}