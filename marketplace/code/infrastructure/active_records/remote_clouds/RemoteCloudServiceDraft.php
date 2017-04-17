<?php
/**
 * Class RemoteCloudServiceDraft
 */
class RemoteCloudServiceDraft
	extends OpenStackImplementationDraft
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