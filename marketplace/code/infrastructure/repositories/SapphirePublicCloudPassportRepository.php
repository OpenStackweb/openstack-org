<?php

/**
 * Copyright 2017 Open Infrastructure Foundation
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
final class SapphirePublicCloudPassportRepository
    extends SapphireRepository
    implements IPublicCloudPassportRepository
{

    public function __construct()
    {
        parent::__construct(null);
    }

    /**
     * @param int $page
     * @param int $page_size
     * @param string $order
     * @param null $search_term
     * @return array
     */
    function getAllCloudsByPage
    (
        $page         = 1,
        $page_size    = 10,
        $order        = null,
        $search_term  = null
    )
    {
        $offset    = ($page - 1 ) * $page_size;
        $order_by  = ' CompanyService.Name ASC ';
        if(!empty($order)){
            if(strstr($order, 'name') !== false)
                $order_by = ' CompanyService.Name '. (strstr($order, '+') !== false ? 'ASC' : 'DESC');
        }

        $order_by = ' ORDER BY '.$order_by;
        $where    = '';

        if(!empty($search_term)){
            if(!empty($where)) $where .= ' AND ';
            $where .= " (Company.Name LIKE '%{$search_term}%' OR CompanyService.Name LIKE '%{$search_term}%') ";
        }

        if(!empty($where)) $where = ' WHERE '.$where;
        $query = <<<SQL
            SELECT
            CompanyService.ID AS CloudID,
            CompanyService.Name AS CloudName,
            CompanyService.Slug AS CloudSlug,
            Company.Name AS CompanyName,
            Company.URLSegment AS CompanySlug,
            Passport.ID AS PassportID,
            Passport.LearnMore,
            Passport.Active
            FROM PublicCloudService
            INNER JOIN CompanyService ON CompanyService.ID = PublicCloudService.ID
            INNER JOIN Company ON Company.ID = CompanyService.CompanyID
            LEFT JOIN PublicCloudPassport AS Passport ON Passport.PublicCloudID = CompanyService.ID
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

    /**
     * @param int $start
     * @param int $limit
     * @param string $order
     * @param null $search_term
     * @return array
     */
    function getAllPassports
    (
        $start         = 1,
        $limit         = 10,
        $order         = 'random',
        $search_term   = null
    )
    {
        $order_by  = ' CompanyService.Name ASC ';
        if(!empty($order)){
            if(strstr($order, 'name') !== false)
                $order_by = ' CompanyService.Name '. (strstr($order, '+') !== false ? 'ASC' : 'DESC');
            if($order == 'random')
                $order_by = 'RAND()';
        }

        $order_by = ' ORDER BY '.$order_by;
        $where    = ' WHERE Passport.Active = 1 ';

        if(!empty($search_term)){
            $where .= " AND (Company.Name LIKE '%{$search_term}%' OR CompanyService.Name LIKE '%{$search_term}%') ";
        }

        $query = <<<SQL
            SELECT
            CompanyService.ID AS CloudID,
            CompanyService.Name AS CloudName,
            CompanyService.Slug AS CloudSlug,
            CompanyService.Overview AS CloudOverview,
            CompanyService.LastEdited AS LastEdited,
            Company.Name AS CompanyName,
            Company.URLSegment AS CompanySlug,
            F.Filename AS Logo,
            Passport.ID AS PassportID,
            Passport.LearnMore,
            Passport.Active,
            GROUP_CONCAT(Loc.Lat SEPARATOR ',') AS Lat,
            GROUP_CONCAT(Loc.Lng SEPARATOR ',') AS Lng,
            GROUP_CONCAT(CONCAT(Loc.City, '(', Loc.Country, ')') SEPARATOR ', ') AS Locations
            FROM PublicCloudService
            INNER JOIN CompanyService ON CompanyService.ID = PublicCloudService.ID
            INNER JOIN Company ON Company.ID = CompanyService.CompanyID
            INNER JOIN PublicCloudPassport AS Passport ON Passport.PublicCloudID = CompanyService.ID
            LEFT JOIN DataCenterLocation AS Loc ON Loc.CloudServiceID = CompanyService.ID
            LEFT JOIN File AS F ON F.ID = Company.LogoID
            {$where} GROUP BY CloudID
            {$order_by}
SQL;

        $count = DB::query($query)->numRecords();

        $query .= <<<SQL
            LIMIT {$start},{$limit};
SQL;

        $list = new ArrayList();
        foreach(DB::query($query) as $row)
        {
            $list->push(new ArrayData($row));
        }

        return [$list, $count];
    }

}