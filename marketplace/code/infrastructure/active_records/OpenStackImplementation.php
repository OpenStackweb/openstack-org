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
 * Class OpenStackImplementation
 */
class OpenStackImplementation
    extends RegionalSupportedCompanyService
    implements IOpenStackImplementation
{

    static $db = array(
        'CompatibleWithCompute'           => 'Boolean',
        'CompatibleWithStorage'           => 'Boolean',
        'CompatibleWithFederatedIdentity' => 'Boolean',
    );

    static $has_one = array('ProgramVersion' => 'InteropProgramVersion');

    static $many_many = array(
        'HyperVisors' => 'HyperVisorType',
        'Guests'      => 'GuestOSType',
    );

    static $has_many = array(
        'Capabilities' => 'OpenStackImplementationApiCoverage'
    );

    private static $defaults = array(
        'CompatibleWithCompute' => false,
        'CompatibleWithStorage' => false,
        'CompatibleWithFederatedIdentity' => false,
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
        return $this->isCompatibleWithStorage() && $this->isCompatibleWithCompute();
    }

    /***
     * @return bool
     */
    public function isCompatibleWithFederatedIdentity()
    {
        return (bool)$this->getField('CompatibleWithFederatedIdentity');
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
}