<?php

/**
 * Copyright 2015 OpenStack Foundation
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
final class OpenStackSampleConfig extends DataObject
{

    private static $create_table_options = array('MySQLDatabase' => 'ENGINE=InnoDB');

    private static $db = array
    (
        'Title'       => 'Varchar',
        'Summary'     => 'HTMLText',
        'Description' => 'HTMLText',
        'IsDefault'   => 'Boolean',
        'Order'       => 'Int',
    );

    private static $has_one = array
    (
        "Curator" => "Member",
        "Release" => "OpenStackRelease",
        "Type"    => 'OpenStackSampleConfigurationType',
    );

    static $many_many = array
    (
        'OpenStackComponents' => 'OpenStackComponent',
    );

    private static $many_many_extraFields = array
    (
        'OpenStackComponents'  => array
        (
            'Order' => 'Int',
        )
    );

    public function getOptionalComponents()
    {
        $release_id = $this->ReleaseID;
        $id         = $this->ID;
$query = <<<SQL
SELECT DISTINCT CASE WHEN OpenStackComponent.ClassName IN ('OpenStackSampleConfig_OpenStackComponents')
THEN OpenStackSampleConfig_OpenStackComponents.`Order`
WHEN OpenStackComponent.ClassName IN ('OpenStackComponent') THEN OpenStackComponent.`Order` ELSE NULL END AS `Order`,
OpenStackComponent.ClassName, OpenStackComponent.Created,
OpenStackComponent.LastEdited, OpenStackComponent.Name, OpenStackComponent.CodeName,
OpenStackComponent.Description, OpenStackComponent.SupportsVersioning, OpenStackComponent.SupportsExtensions,
OpenStackComponent.IsCoreService, OpenStackComponent.IconClass, OpenStackComponent.`Use`,
OpenStackComponent.HasStableBranches, OpenStackComponent.WikiUrl, OpenStackComponent.TCApprovedRelease,
OpenStackComponent.ReleaseMileStones, OpenStackComponent.ReleaseCycleWithIntermediary, OpenStackComponent.ReleaseIndependent,
OpenStackComponent.HasTeamDiversity, OpenStackComponent.IncludedComputeStarterKit, OpenStackComponent.VulnerabilityManaged,
OpenStackRelease_OpenStackComponents.Adoption,
OpenStackRelease_OpenStackComponents.MaturityPoints,
OpenStackRelease_OpenStackComponents.HasInstallationGuide,
OpenStackRelease_OpenStackComponents.SDKSupport,
OpenStackComponent.LatestReleasePTLID, OpenStackComponent.ID, CASE WHEN OpenStackComponent.ClassName IS NOT NULL
THEN OpenStackComponent.ClassName ELSE 'OpenStackComponent' END AS RecordClassName FROM OpenStackComponent
INNER JOIN OpenStackSampleConfig_OpenStackComponents ON OpenStackSampleConfig_OpenStackComponents.OpenStackComponentID = OpenStackComponent.ID
INNER JOIN OpenStackRelease_OpenStackComponents ON OpenStackRelease_OpenStackComponents.OpenStackReleaseID = {$release_id}
AND OpenStackRelease_OpenStackComponents.OpenStackComponentID = OpenStackSampleConfig_OpenStackComponents.OpenStackComponentID
WHERE (OpenStackSampleConfig_OpenStackComponents.OpenStackSampleConfigID = {$id}) AND (OpenStackComponent.IsCoreService = 0)
SQL;


        $rows = DB::query($query);
        $list = new ArrayList();
        foreach($rows as $row)
        {
            $class = $row['ClassName'];
            $list->add(new $class($row));
        }
        return $list;
    }

    public function getCoreComponents()
    {
        return $this->OpenStackComponents()->filter('IsCoreService', true);
    }

}