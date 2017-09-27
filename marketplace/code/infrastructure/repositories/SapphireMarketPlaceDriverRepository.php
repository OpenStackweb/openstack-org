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
final class SapphireMarketPlaceDriverRepository
    extends SapphireRepository
    implements IMarketPlaceDriverRepository
{

    public function __construct()
    {
        parent::__construct(null);
    }

    /**
     * @param string $order
     * @param bool $filters
     * @return array
     */
    function getAllByFilter ($order = 'Project ASC', $filters = array())
    {
        $filter_clause = '';

        if ($filters['project'] != 'all') {
            $filter_clause .= " AND LCASE(D.Project) = '".$filters['project']."'";
        }

        if ($filters['release'] != 'all') {
            $filter_clause .= " AND LCASE(R.Name) = '".$filters['release']."'";
        }

        if ($filters['vendor'] != 'all') {
            $filter_clause .= " AND LCASE(D.Vendor) = '".$filters['vendor']."'";
        }

        $query = <<<SQL
            SELECT D.* FROM Driver D
            LEFT JOIN Driver_Releases DR ON DR.DriverID = D.ID
            LEFT JOIN DriverRelease R ON R.ID = DR.DriverReleaseID
            WHERE D.Active = 1
            {$filter_clause}
            GROUP BY D.ID
            ORDER BY D.{$order}
SQL;

        $list = new ArrayList();
        foreach(DB::query($query) as $row)
        {
            $list->push(new ArrayData($row));
        }

        return $list;
    }


}