<?php
/**
 * Copyright 2018 OpenStack Foundation
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
 * Class SapphireAUCMetricRepository
 */
final class SapphireAUCMetricRepository
    extends SapphireRepository
    implements IAUCMetricRepository
{

    /**
     * @param int $page
     * @param int $page_size
     * @param string $order
     * @param null|string $search_term
     * @param null|string $type
     * @param array $filter
     * @return array
     */
    function getAllByPage
    (
        $page = 1,
        $page_size = 10,
        $order = '',
        $search_term = null,
        $type = null,
        array $filter = []
    )
    {
        $offset    = ($page - 1 ) * $page_size;
        $order_by  = ' Member.ID ASC ';
        if(!empty($order)){
            if(strstr($order, 'id') !== false)
                $order_by = ' AUCMetric.ID '. (strstr($order, '+') !== false ? 'ASC' : 'DESC');
            if(strstr($order, 'member_id') !== false)
                $order_by = ' Member.ID '. (strstr($order, '+') !== false ? 'ASC' : 'DESC');
            if(strstr($order, 'identifier') !== false)
                $order_by = ' AUCMetric.Identifier '. (strstr($order, '+') !== false ? 'ASC' : 'DESC');
            if(strstr($order, 'created') !== false)
                $order_by = ' AUCMetric.Created '. (strstr($order, '+') !== false ? 'ASC' : 'DESC');
            if(strstr($order, 'expires') !== false)
                $order_by = ' AUCMetric.Expires '. (strstr($order, '+') !== false ? 'ASC' : 'DESC');
        }

        $order_by = ' ORDER BY '.$order_by;

        $where    = '';

        if(!empty($search_term)){
            if(!empty($where)) $where .= ' AND ';
            $where .= " ( ValueDescription LIKE '%{$search_term}%' 
            OR Value LIKE '%{$search_term}%' 
            OR Member.Email LIKE '%{$search_term}%'
            OR Member.SecondEmail LIKE '%{$search_term}%'
            OR Member.ThirdEmail LIKE '%{$search_term}%' 
            OR Member.FirstName LIKE '%{$search_term}%' 
            OR Member.Surname LIKE '%{$search_term}%' ) ";
        }

        if(!empty($type) && $type != "ALL"){
            if(!empty($where)) $where .= ' AND ';
            $where .= " ( Identifier =  '$type' ) ";
        }

        $filter = self::parseFilters($filter);

        if(count($filter)){
            $where_filter = '';
            foreach ($filter as $e){
                $field = '';
                switch ($e[0]){
                    case 'from_date':
                    case 'to_date':
                    {
                        $field = 'AUCMetric.Created';
                    }
                    break;
                }
                if(!empty($field)){
                    if(!empty($where_filter)) $where_filter .= ' AND ';
                    $where_filter .= sprintf("%s %s '%s'", $field, $e[1], $e[2]);
                }
            }
            if(!empty($where_filter)){
                if(!empty($where)) $where .= ' AND ';
                $where .= " ( ".$where_filter." ) ";
            }
        }

        if(!empty($where)) $where = ' WHERE '.$where;
        $query = <<<SQL
SELECT
AUCMetric.*
FROM AUCMetric
LEFT JOIN Member ON Member.ID = FoundationMemberID
{$where}
{$order_by}
SQL;

        $count = DB::query($query)->numRecords();

        $query .= <<<SQL
        LIMIT {$offset},{$page_size};
SQL;

        $list = new ArrayList();
        foreach(DB::query($query) as $row)
        {
            $list->push(new AUCMetric($row));
        }

        return [$list, $count];
    }
}