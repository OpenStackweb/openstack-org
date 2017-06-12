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
final class SapphireOpenStackPoweredServiceRepository
    extends SapphireRepository
    implements IOpenStackPoweredServiceRepository
{

    public function __construct()
    {
        parent::__construct(null);
    }

    /**
     * @param int $page
     * @param int $page_size
     * @param string $order
     * @param bool $filters
     * @param null $search_term
     * @param string $service_type
     * @return array
     */
    function getAllByPage
    (
        $page         = 1,
        $page_size    = 10,
        $order        = null,
        $filters      = array(),
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

            if(strstr($order, 'expiry_date') !== false)
                $order_by = ' ExpiryDate '.(strstr($order, '+') !== false ? 'ASC' : 'DESC');
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
        }

        if(!empty($search_term)){
            if(!empty($where)) $where .= ' AND ';
            $where .= " (Company.Name LIKE '%{$search_term}%' OR CompanyService.Name LIKE '%{$search_term}%') ";
        }

        if(isset($filters['expired']) && $filters['expired']){
            if(!empty($where)) $where .= ' AND ';
            $where .= " OpenStackImplementation.ExpiryDate < NOW()";
        }

        if(isset($filters['powered']) && $filters['powered']){
            if(!empty($where)) $where .= ' AND ';
            $where .= " (CompatibleWithStorage = 1 OR CompatibleWithCompute = 1) ";
        }

        if(!empty($where)) $where = ' WHERE '.$where;
        $query = <<<SQL
SELECT  
CompanyService.ID,
CompatibleWithStorage,
CompatibleWithCompute,
CompatibleWithFederatedIdentity,
ProgramVersionID,
InteropProgramVersion.Name AS ProgramVersionName,
ExpiryDate,
CompanyService.ClassName,
CompanyService.Name,
Company.Name AS CompanyName,
H.Email AS LastEditedBy
FROM OpenStackImplementation
INNER JOIN CompanyService ON CompanyService.ID = OpenStackImplementation.ID
INNER JOIN Company ON Company.ID = CompanyService.CompanyID
LEFT JOIN InteropProgramVersion ON InteropProgramVersion.ID = OpenStackImplementation.ProgramVersionID
LEFT JOIN 
( SELECT OpenStackPoweredProgramHistory.*, Member.Email 
  FROM OpenStackPoweredProgramHistory 
  INNER JOIN Member ON Member.ID = OpenStackPoweredProgramHistory.OwnerID
) AS H
ON H.ID = 
(
		SELECT MAX(OpenStackPoweredProgramHistory.ID) 
		FROM OpenStackPoweredProgramHistory 
		WHERE OpenStackPoweredProgramHistory.OpenStackImplementationID = OpenStackImplementation.ID
)

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
     * @return array
     */
    function getAllExpired()
    {
       list($list, $count) = $this->getAllByPage(1, PHP_INT_MAX,null, false);
       return $list;
    }

    /**
     * @param int $days qty of days
     * @return array
     */
    function getAllAboutToExpireOn($days)
    {
        $query = <<<SQL
SELECT  
CompanyService.ID,
CompatibleWithStorage,
CompatibleWithCompute,
CompatibleWithFederatedIdentity,
ProgramVersionID,
InteropProgramVersion.Name AS ProgramVersionName,
ExpiryDate,
CompanyService.ClassName,
CompanyService.Name,
Company.Name AS CompanyName,
H.Email AS LastEditedBy
FROM OpenStackImplementation
INNER JOIN CompanyService ON CompanyService.ID = OpenStackImplementation.ID
INNER JOIN Company ON Company.ID = CompanyService.CompanyID
LEFT JOIN InteropProgramVersion ON InteropProgramVersion.ID = OpenStackImplementation.ProgramVersionID
LEFT JOIN 
( SELECT OpenStackPoweredProgramHistory.*, Member.Email 
  FROM OpenStackPoweredProgramHistory 
  INNER JOIN Member ON Member.ID = OpenStackPoweredProgramHistory.OwnerID
) AS H
ON H.ID = 
(
		SELECT MAX(OpenStackPoweredProgramHistory.ID) 
		FROM OpenStackPoweredProgramHistory 
		WHERE OpenStackPoweredProgramHistory.OpenStackImplementationID = OpenStackImplementation.ID
)
WHERE ExpiryDate IS NOT NULL AND DATEDIFF(ExpiryDate, NOW() ) <= {$days} AND DATEDIFF(ExpiryDate, NOW() ) >= 0
SQL;
        $list = new ArrayList();
        foreach(DB::query($query) as $row)
        {
            $list->push(new ArrayData($row));
        }

        return $list;
    }
}