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
 * Class SapphireCLAMemberRepository
 */
final class SapphireCLAMemberRepository
    extends SapphireMemberRepository
    implements ICLAMemberRepository
{

    public function __construct()
    {
        $entity = new ICLAMemberDecorator;
        $entity->setOwner(new Member);
        parent::__construct($entity);
    }

    /**
     * @param int $offset
     * @param int $limit
     * @return ICLAMember[]
     */
    function getAllICLAMembers($offset, $limit)
    {
        return $this->getAllICLAMembersByFilter($offset, $limit);
    }

    /**
     * @param string $email
     * @param int $offset
     * @param int $limit
     * @return array
     */
    function getAllIClaMembersByEmail($email, $offset, $limit)
    {
        return $this->getAllICLAMembersByFilter($offset, $limit , ["Email" => $email]);
    }

    /**
     * @param string $first_name
     * @param int $offset
     * @param int $limit
     * @return array
     */
    function getAllIClaMembersByFirstName($first_name, $offset, $limit)
    {
        return $this->getAllICLAMembersByFilter($offset, $limit , ["FirstName" => $first_name]);
    }

    /**
     * @param string $last_name
     * @param int $offset
     * @param int $limit
     * @return array
     */
    function getAllIClaMembersByLastName($last_name, $offset, $limit)
    {
        return $this->getAllICLAMembersByFilter($offset, $limit , ["Surname" => $last_name]);
    }

    private function getAllICLAMembersByFilter($offset, $limit, array $filters = []){

        $base_query = <<<SQL
FROM Member WHERE EXISTS (SELECT ID FROM GerritUser WHERE GerritUser.MemberID = Member.ID)
SQL;

        $extra_where = '';
        if(count($filters) > 0){
            foreach ($filters as $key => $val){
                $extra_where .= " AND {$key} LIKE '%{$val}%' ";
            }
        }

        $query_count  = DB::query("SELECT COUNT(ID) AS QTY {$base_query} {$extra_where};");
        $total        = intval($query_count->column('QTY')[0]);
        $query_select = DB::query("SELECT * {$base_query} {$extra_where} LIMIT {$limit} OFFSET {$offset};");

        $res = [];

        foreach ($query_select as $row){
            $res[] = new Member($row);
        }

        return [$res, $total];
    }
}