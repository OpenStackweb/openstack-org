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
 * Class Distribution
 */
class RemoteCloudService
	extends OpenStackImplementation
	implements IRemoteCloudService {

    static $db = array(
        'HardwareSpecifications'    => 'Text',
        'VendorManagedUpgrades'     => 'Boolean',
        'PricingModels'             => 'Text',
        'PublishedSLAs'             => 'Text',
    );

    /***
     * @return string
     */
    public function getHardwareSpecifications(){
        return $this->getField('HardwareSpecifications');
    }

    /**
     * @param string $specs
     * @return void
     */
    public function setHardwareSpecifications($specs){
        $this->setField('HardwareSpecifications', $specs);
    }

    /***
     * @return bool
     */
    public function hasVendorManagedUpgrades(){
        return (bool)$this->getField('VendorManagedUpgrades');
    }
    /**
     * @param bool $is_managed
     * @return void
     */
    public function setVendorManagedUpgrades($is_managed){
        $this->setField('VendorManagedUpgrades', $is_managed);
    }

    /***
     * @return string
     */
    public function getPricingModels(){
        return $this->getField('PricingModels');
    }

    /**
     * @param string $models
     * @return void
     */
    public function setPricingModels($models){
        $this->setField('PricingModels', $models);
    }

    /***
     * @return string
     */
    public function getPublishedSLAs(){
        return $this->getField('PublishedSLAs');
    }

    /**
     * @param string $slas
     * @return void
     */
    public function setPublishedSLAs($slas){
        $this->setField('PublishedSLAs', $slas);
    }

} 