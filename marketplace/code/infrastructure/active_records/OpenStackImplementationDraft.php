<?php

/**
 * Class OpenStackImplementationDraft
 */
class OpenStackImplementationDraft
    extends RegionalSupportedCompanyServiceDraft
    implements IOpenStackImplementation
{

    static $db = [
        'CompatibleWithCompute'             => 'Boolean',
        'CompatibleWithStorage'             => 'Boolean',
        'CompatibleWithPlatform'            => 'Boolean',
        'ExpiryDate'                        => 'SS_Datetime',
        'CompatibleWithFederatedIdentity'   => 'Boolean',
    ];

    static $has_one = array('ProgramVersion' => 'InteropProgramVersion');

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

    /***
     * @return bool
     */
    public function isCompatibleWithPlatform()
    {
        return $this->isCompatibleWithStorage() && $this->isCompatibleWithCompute();
    }

    /**
     * @param IInteropProgramVersion $program_version
     * @return void
     */
    public function setProgramVersion(IInteropProgramVersion $program_version)
    {
        $this->ProgramVersionID = $program_version->getIdentifier();
    }

    /**
     * @return IInteropProgramVersion
     */
    public function getProgramVersion()
    {
        $program_version =  $this->ProgramVersion();
        UnitOfWork::getInstance()->scheduleForUpdate($program_version);
        return $program_version;
    }

    /***
     * @return bool
     */
    public function isOpenStackTested()
    {
        $storage = $this->isCompatibleWithStorage();
        $compute = $this->isCompatibleWithCompute();
        $platform = $this->isCompatibleWithPlatform();
        $identity = $this->isCompatibleWithFederatedIdentity();

        return $storage || $compute || $platform || $identity;
    }

    /**
     * @return string
     */
    public function getTestedCapabilityTypeLabel()
    {
        if ($this->isCompatibleWithPlatform()) {
            return 'Platform';
        } else if ($this->isCompatibleWithCompute()) {
            return 'Compute';
        } else if ($this->isCompatibleWithStorage()) {
            return 'Storage';
        }
    }

    public function getTestedCapabilities() {
        $program_type = '';
        if ($this->isCompatibleWithPlatform()) {
            $program_type = '';
        } else if ($this->isCompatibleWithCompute()) {
            $program_type = 'OpenStack Powered Compute';
        } else if ($this->isCompatibleWithStorage()) {
            $program_type = 'OpenStack Powered Object Storage';
        }

        return $this->getProgramVersion()->getCapabilitiesByProgramType($program_type);
    }

    public function getDesignatedSections() {
        $program_type = '';
        if ($this->isCompatibleWithPlatform()) {
            $program_type = '';
        } else if ($this->isCompatibleWithCompute()) {
            $program_type = 'OpenStack Powered Compute';
        } else if ($this->isCompatibleWithStorage()) {
            $program_type = 'OpenStack Powered Object Storage';
        }

        return $this->getProgramVersion()->getDesignatedSectionsByProgramType($program_type);
    }

    /**
     * @return bool
     */
    public function isOpenStackPoweredExpired()
    {
        return false;
    }
}