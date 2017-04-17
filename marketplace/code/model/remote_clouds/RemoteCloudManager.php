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
 * Class RemoteCloudManager
 */
final class RemoteCloudManager extends OpenStackImplementationManager {

	/**
	 * @return IMarketPlaceType
	 * @throws NotFoundEntityException
	 */
	protected function getMarketPlaceType()
	{
		$marketplace_type =  $this->marketplace_type_repository->getByType(IRemoteCloudService::MarketPlaceType);
		if(!$marketplace_type)
			throw new NotFoundEntityException('MarketPlaceType',sprintf("type %s ",IRemoteCloudService::MarketPlaceType));

		return $marketplace_type;
	}

    public function buildCompanyService($data, $company, $getMarketPlaceType, $live_service_id)
    {
        $service = parent::buildCompanyService($data, $company, $getMarketPlaceType, $live_service_id);
        $this->factory->buildExtras($service, $data);
        return $service;
    }

    public function update($remote_cloud, $data)
    {
        $remote_cloud = parent::update($remote_cloud, $data);
        $remote_cloud->setHardwareSpecifications($data['hardware_specifications']);
        $remote_cloud->setVendorManagedUpgrades($data['vendor_managed_upgrades']);
        $remote_cloud->setPricingModels($data['pricing_models']);
        $remote_cloud->setPublishedSLAs($data['published_slas']);

        return $remote_cloud;
    }

} 