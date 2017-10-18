<?php
/**
 * Copyright 2017 OpenStack Foundation
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
 * Class SapphireCloudServiceRepository
 */
class SapphireCloudServiceRepository
    extends SapphireOpenStackImplementationRepository
    implements ICloudServiceRepository
{

    /**
     * @param int $page
     * @param int $page_size
     * @param string $order
     * @param array $filters
     * @param null|string $search_term
     * @param string $service_type
     * @return array
     */
    function getAllByPage
    (
        $page        = 1,
        $page_size   = 10,
        $order       = '',
        $filters     = [],
        $search_term = null,
        $service_type = 'ALL'
    )
    {
        $offset    = ($page - 1 ) * $page_size;
        $order_by  = ' CompanyService.Name ASC ';

        if(!empty($order)){
            if(strstr($order, 'name') !== false)
                $order_by = ' CompanyService.Name '. (strstr($order, '+') !== false ? 'ASC' : 'DESC');

            if(strstr($order, 'type') !== false)
                $order_by = ' CompanyService.ClassName '. (strstr($order, '+') !== false ? 'ASC' : 'DESC');

        }

        $order_by = ' ORDER BY '.$order_by;
        $where    = '';

        switch ($service_type){
            case 'PUBLICCLOUD':
                $where .= " CompanyService.ClassName = 'PublicCloudService' ";
                break;
            case 'PRIVATECLOUD':
                $where .= " CompanyService.ClassName = 'PrivateCloudService' ";
                break;
            default:
                $where .= " CompanyService.ClassName IN ('PrivateCloudService', 'PublicCloudService') ";
                break;
        }

        if(!empty($search_term)){
            if(!empty($where)) $where .= ' AND ';
            $where .= " (Company.Name LIKE '%{$search_term}%' OR CompanyService.Name LIKE '%{$search_term}%') ";
        }

        if(!empty($where)) $where = ' WHERE '.$where;
        $query = <<<SQL
SELECT  
CompanyService.ID,
CompanyService.ClassName,
CompanyService.Name,
Company.Name AS CompanyName,
COUNT(DataCenterLocation.ID) AS DataCentersQty,
COUNT(DISTINCT(DataCenterLocation.Country)) AS DataCentersCountryQty,
group_concat(DataCenterLocation.City) AS DataCentersLocations
FROM OpenStackImplementation
INNER JOIN CompanyService ON CompanyService.ID = OpenStackImplementation.ID
INNER JOIN Company ON Company.ID = CompanyService.CompanyID
LEFT JOIN DataCenterLocation ON DataCenterLocation.CloudServiceID = CompanyService.ID
{$where}
GROUP BY CompanyService.ID,
CompanyService.ClassName,
CompanyService.Name,
Company.Name
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
     * @return string
     */
    protected function getMarketPlaceTypeGroup()
    {
        return null;
    }
}