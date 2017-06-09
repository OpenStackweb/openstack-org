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
final class SapphireRegionalServiceRepository
    extends SapphireRepository
    implements IRegionalServiceRepository
{

    public function __construct()
    {
        parent::__construct(null);
    }

    /**
     * @param int $page
     * @param int $page_size
     * @param string $order
     * @param bool $show_all
     * @param null $search_term
     * @param string $service_type
     * @return array
     */
    function getAllByPage
    (
        $page         = 1,
        $page_size    = 10,
        $order        = null,
        $show_all     = true,
        $search_term  = null,
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
            case 'DISTRIBUTION':
                $where .= " CompanyService.ClassName = 'Distribution' ";
                break;
            case 'APPLIANCE':
                $where .= " CompanyService.ClassName = 'Appliance' ";
                break;
            case 'REMOTECLOUD':
                $where .= " CompanyService.ClassName = 'RemoteCloudService' ";
                break;
            case 'PUBLICCLOUD':
                $where .= " CompanyService.ClassName = 'PublicCloudService' ";
                break;
            case 'PRIVATECLOUD':
                $where .= " CompanyService.ClassName = 'PrivateCloudService' ";
                break;
            case 'CONSULTANT':
                $where .= " CompanyService.ClassName = 'Consultant' ";
                break;
        }

        if(!empty($search_term)){
            if(!empty($where)) $where .= ' AND ';
            $where .= " (Company.Name LIKE '%{$search_term}%' OR CompanyService.Name '%{$search_term}%') ";
        }

        if(!empty($where)) $where = ' WHERE '.$where;
        $query = <<<SQL
SELECT
CompanyService.ID,
ProgramVersionID,
InteropProgramVersion.Name AS ProgramVersionName,
CompanyService.ClassName,
CompanyService.Name,
Company.Name AS CompanyName,
GROUP_CONCAT(DISTINCT L.City SEPARATOR ', ') AS City,
GROUP_CONCAT(DISTINCT Countries.Name SEPARATOR ', ') AS Country,
CONCAT_WS(',',GROUP_CONCAT(DISTINCT DCR.Name),GROUP_CONCAT(DISTINCT DCR2.Name),GROUP_CONCAT(DISTINCT Region.Name)) AS RegionName,
GROUP_CONCAT(DISTINCT Member.Email SEPARATOR ', ') AS Admins,
OpenStackImplementation.Notes AS Notes
FROM CompanyService
LEFT JOIN OpenStackImplementation ON CompanyService.ID = OpenStackImplementation.ID
LEFT JOIN Company ON Company.ID = CompanyService.CompanyID
LEFT JOIN InteropProgramVersion ON InteropProgramVersion.ID = OpenStackImplementation.ProgramVersionID
LEFT JOIN RegionalSupport ON RegionalSupport.ServiceID = CompanyService.ID
LEFT JOIN Region ON RegionalSupport.RegionID = Region.ID
LEFT JOIN DataCenterLocation L ON L.CloudServiceID = CompanyService.ID
LEFT JOIN DataCenterRegion DCR ON DCR.ID = L.DataCenterRegionID
LEFT JOIN DataCenterRegion DCR2 ON DCR2.CloudServiceID = CompanyService.ID
LEFT JOIN Countries ON Countries.Code = L.Country
LEFT JOIN Company_Administrators ON Company_Administrators.CompanyID = Company.ID
LEFT JOIN Member ON Member.ID = Company_Administrators.MemberID
{$where}
GROUP BY CompanyService.ID
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