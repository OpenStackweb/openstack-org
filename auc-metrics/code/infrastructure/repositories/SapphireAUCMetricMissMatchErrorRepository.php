<?php
/**
 * Copyright 2018 Open Infrastructure Foundation
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
 * Class SapphireAUCMetricMissMatchErrorRepository
 */
final class SapphireAUCMetricMissMatchErrorRepository
    extends SapphireRepository
    implements IAUCMetricMissMatchErrorRepository
{

    public function __construct()
    {
        parent::__construct(new AUCMetricMissMatchError);
    }

    /**
     * @param int $page
     * @param int $page_size
     * @param string $order
     * @param null|string $search_term
     * @param null|string $type
     * @return array
     */
    function getAllByPage
    (
        $page = 1,
        $page_size = 10,
        $order = '',
        $search_term = null,
        $type        = null
    )
    {
        $offset    = ($page - 1 ) * $page_size;
        $order_by  = ' AUCMetricMissMatchError.ID ASC ';
        if(!empty($order)){
            if(strstr($order, 'id') !== false)
                $order_by = ' AUCMetricMissMatchError.ID '. (strstr($order, '+') !== false ? 'ASC' : 'DESC');
            if(strstr($order, 'service_identifier') !== false)
                $order_by = ' AUCMetricMissMatchError.ServiceIdentifier '. (strstr($order, '+') !== false ? 'ASC' : 'DESC');
            if(strstr($order, 'user_identifier') !== false)
                $order_by = ' AUCMetricMissMatchError.UserIdentifier '. (strstr($order, '+') !== false ? 'ASC' : 'DESC');
        }

        $order_by = ' ORDER BY '.$order_by;
        // not solved
        $where    = ' Solved = 0 ';

        if(!empty($search_term)){
            if(!empty($where)) $where .= ' AND ';
            $where .= " ( UserIdentifier LIKE '%{$search_term}%' ) ";
        }

        if(!empty($type) && $type != "ALL"){
            if(!empty($where)) $where .= ' AND ';
            $where .= " ( ServiceIdentifier =  '$type' ) ";
        }

        if(!empty($where)) $where = ' WHERE '.$where;
        $query = <<<SQL
SELECT
*
FROM AUCMetricMissMatchError
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
            $list->push(new ArrayData($row));
        }

        return [$list, $count];
    }
}