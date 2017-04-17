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
 * Interface IRemoteCloudService
 */
interface IRemoteCloudService extends IOpenStackImplementation {
	const MarketPlaceType           = 'Remote Cloud';
	const MarketPlaceGroupSlug      = 'marketplace-remote-cloud-administrators';
	const MarketPlacePermissionSlug = 'MANAGE_MARKETPLACE_REMOTE_CLOUD';

    /***
     * @return string
     */
    public function getHardwareSpecifications();

    /**
     * @param string $specs
     * @return void
     */
    public function setHardwareSpecifications($specs);

    /***
     * @return bool
     */
    public function hasVendorManagedUpgrades();

    /**
     * @param bool $is_managed
     * @return void
     */
    public function setVendorManagedUpgrades($is_managed);

    /***
     * @return string
     */
    public function getPricingModels();

    /**
     * @param string $models
     * @return void
     */
    public function setPricingModels($models);

    /***
     * @return string
     */
    public function getPublishedSLAs();

    /**
     * @param string $slas
     * @return void
     */
    public function setPublishedSLAs($slas);
}