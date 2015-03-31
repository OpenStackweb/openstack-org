<?php

/**
 * Class OpenStackImplementationDraft
 */
class OpenStackImplementationDraft
    extends RegionalSupportedCompanyServiceDraft
    implements IOpenStackImplementation
{

    static $db = array(
        'CompatibleWithCompute' => 'Boolean',
        'CompatibleWithStorage' => 'Boolean',
        'CompatibleWithPlatform' => 'Boolean',
        'CompatibleWithFederatedIdentity' => 'Boolean',
        'ComputeCapabilities' => 'HTMLText',
        'StorageCapabilities' => 'HTMLText',
        'PlatformCapabilities' => 'HTMLText');

    static $many_many = array(
        'HyperVisors' => 'HyperVisorType',
        'Guests' => 'GuestOSType',
    );

    private static $defaults = array(
        'CompatibleWithCompute' => false,
        'CompatibleWithStorage' => false,
        'CompatibleWithPlatform' => false,
        'CompatibleWithFederatedIdentity' => false,
    );

    static $has_many = array(
        'Capabilities' => 'OpenStackImplementationApiCoverageDraft'
    );

    /**
     * @return IHyperVisorType[]
     */
    public function getHyperVisors()
    {
        return AssociationFactory::getInstance()->getMany2ManyAssociation($this, 'HyperVisors')->toArray();
    }

    /**
     * @param IHyperVisorType $hypervisor
     * @return void
     */
    public function addHyperVisor(IHyperVisorType $hypervisor)
    {
        AssociationFactory::getInstance()->getMany2ManyAssociation($this, 'HyperVisors')->add($hypervisor);
    }

    /**
     * @return IGuestOSType[]
     */
    public function getGuests()
    {
        return AssociationFactory::getInstance()->getMany2ManyAssociation($this, 'Guests')->toArray();
    }

    /**
     * @param IGuestOSType $guest
     * @return void
     */
    public function addGuest(IGuestOSType $guest)
    {
        AssociationFactory::getInstance()->getMany2ManyAssociation($this, 'Guests')->add($guest);
    }


    /**
     * @return array|IOpenStackImplementationApiCoverage[]
     */
    public function getCapabilities()
    {
        return AssociationFactory::getInstance()->getOne2ManyAssociation($this, 'Capabilities')->toArray();
    }

    /**
     * @param IOpenStackImplementationApiCoverage $capability
     * @return void
     */
    public function addCapability(IOpenStackImplementationApiCoverage $capability)
    {
        AssociationFactory::getInstance()->getOne2ManyAssociation($this, 'Capabilities')->add($capability);
    }

    public function clearCapabilities()
    {
        AssociationFactory::getInstance()->getOne2ManyAssociation($this, 'Capabilities')->removeAll();
    }

    public function clearHypervisors()
    {
        AssociationFactory::getInstance()->getMany2ManyAssociation($this, 'HyperVisors')->removeAll();
    }

    public function clearGuests()
    {
        AssociationFactory::getInstance()->getMany2ManyAssociation($this, 'Guests')->removeAll();
    }
    /***
     * @return bool
     */
    public function isCompatibleWithCompute()
    {
        return (bool)$this->getField('CompatibleWithCompute');
    }

    /**
     * @param bool $compatible
     * @return void
     */
    public function setCompatibleWithCompute($compatible)
    {
        $this->setField('CompatibleWithCompute', $compatible);
    }

    /***
     * @return bool
     */
    public function isCompatibleWithStorage()
    {
        return (bool)$this->getField('CompatibleWithStorage');
    }

    /**
     * @param bool $compatible
     * @return void
     */
    public function setCompatibleWithStorage($compatible)
    {
        $this->setField('CompatibleWithStorage', $compatible);
    }

    /***
     * @return bool
     */
    public function isCompatibleWithPlatform()
    {
        return (bool)$this->getField('CompatibleWithPlatform');
    }

    /**
     * @param bool $compatible
     * @return void
     */
    public function setCompatibleWithPlatform($compatible)
    {
        $this->setField('CompatibleWithPlatform', $compatible);
    }

    /***
     * @return bool
     */
    public function isCompatibleWithFederatedIdentity()
    {
        return (bool)$this->getField('CompatibleWithFederatedIdentity');
    }

    /**
     * @param bool $compatible
     * @return void
     */
    public function setCompatibleWithFederatedIdentity($compatible)
    {
        $this->setField('CompatibleWithFederatedIdentity', $compatible);
    }

    /**
     * @return string
     */
    public function getComputeCapabilities()
    {
        return (string)$this->getField('ComputeCapabilities');
    }

    /**
     * @param string $capabilities
     * @return void
     */
    public function setComputeCapabilities($capabilities)
    {
        $this->setField('ComputeCapabilities', $capabilities);
    }

    /**
     * @return string
     */
    public function getStorageCapabilities()
    {
        return (string)$this->getField('StorageCapabilities');
    }

    /**
     * @param string $capabilities
     * @return void
     */
    public function setStorageCapabilities($capabilities)
    {
        $this->setField('StorageCapabilities', $capabilities);
    }

    /**
     * @return string
     */
    public function getPlatformCapabilities()
    {
        return (string)$this->getField('PlatformCapabilities');
    }

    /**
     * @param string $capabilities
     * @return void
     */
    public function setPlatformCapabilities($capabilities)
    {
        $this->setField('PlatformCapabilities', $capabilities);
    }
}