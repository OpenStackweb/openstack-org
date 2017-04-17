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
 * Class RemoteCloudAssembler
 */
final class RemoteCloudAssembler
{

    /**
     * @param IRemoteCloudService $remote_cloud
     * @return array
     */
    public static function convertRemoteCloudToArray(IRemoteCloudService $remote_cloud)
    {
        $res = OpenStackImplementationAssembler::convertOpenStackImplementationToArray($remote_cloud);

        //draft
        if ($res) {
            $res['hardware_specifications'] = $remote_cloud->getHardwareSpecifications();
            $res['vendor_managed_upgrades'] = $remote_cloud->hasVendorManagedUpgrades();
            $res['pricing_models'] = $remote_cloud->getPricingModels();
            $res['published_slas'] = $remote_cloud->getPublishedSLAs();
            return $res;
        }
    }

} 