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

    // OpenStack Powered Program attributes
    static $db = [
        'CompatibleWithCompute' => 'Boolean',
        'CompatibleWithStorage' => 'Boolean',
        'CompatibleWithFederatedIdentity' => 'Boolean',
        'UsesIronic' => 'Boolean',
        'ExpiryDate' => 'SS_Datetime',
        'Notes' => 'Text',
        'CompatibleWithDNS' => 'Boolean',
        'CompatibleWithOrchestration' => 'Boolean',
        'CompatibleWithSharedFileSystem' => 'Boolean',
        'CompatibleWithPlatform' => 'Boolean',
    ];

    // OpenStack Powered Program attributes
    static $has_one = [
        'ProgramVersion' => 'InteropProgramVersion',
        'ReportedRelease' => 'OpenStackRelease',
        'PassedRelease' => 'OpenStackRelease',
    ];

    static $many_many = [
        'HyperVisors' => 'HyperVisorType',
        'Guests' => 'GuestOSType',
    ];

    static $has_many = [
        'Capabilities' => 'OpenStackImplementationApiCoverage',
        'StateSnapshots' => 'OpenStackPoweredProgramHistory',
        'ZenDeskLinks' => 'ZenDeskLink',
        'RefStackLinks' => 'RefStackLink',
    ];

    private static $defaults = [
        'CompatibleWithCompute' => false,
        'CompatibleWithStorage' => false,
        'CompatibleWithDNS' => false,
        'CompatibleWithOrchestration' => false,
        'CompatibleWithFederatedIdentity' => false,
        'UsesIronic' => false,
        'CompatibleWithSharedFileSystem' => false,
        'CompatibleWithPlatform' => false
    ];

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
    public function isCompatibleWithDNS()
    {
        return (bool)$this->getField('CompatibleWithDNS');
    }

    /**
     * @param bool $compatible
     * @return void
     */
    public function setCompatibleWithDNS($compatible)
    {
        $this->setField('CompatibleWithDNS', $compatible);
    }

    /***
     * @return bool
     */
    public function isCompatibleWithOrchestration():bool
    {
        return (bool)$this->getField('CompatibleWithOrchestration');
    }

    /**
     * @param bool $compatible
     * @return void
     */
    public function setCompatibleWithOrchestration($compatible)
    {
        $this->setField('CompatibleWithOrchestration', $compatible);
    }

    /***
     * @return bool
     */
    public function isCompatibleWithPlatform()
    {
        return (bool)$this->getField('CompatibleWithPlatform');
    }

    public function setCompatibleWithPlatform($compatible){
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

    /***
     * @return bool
     */
    public function isCompatibleWithSharedFileSystem()
    {
        return (bool)$this->getField('CompatibleWithSharedFileSystem');
    }

    /**
     * @param bool $compatible
     * @return void
     */
    public function setCompatibleWithSharedFileSystem($compatible)
    {
        $this->setField('CompatibleWithSharedFileSystem', $compatible);
    }
    /***
     * @return bool
     */
    public function getUsesIronic()
    {
        return (bool)$this->getField('UsesIronic');
    }

    /**
     * @param bool $compatible
     * @return void
     */
    public function setUsesIronic($compatible)
    {
        $this->setField('UsesIronic', $compatible);
    }

    /***
     * @return bool
     */
    public function isOpenStackPowered():bool
    {
        $storage = $this->isCompatibleWithStorage();
        $compute = $this->isCompatibleWithCompute();
        $platform = $this->isCompatibleWithPlatform();
        $dns = $this->isCompatibleWithDNS();
        $orchestration = $this->isCompatibleWithOrchestration();
        $sharedFS = $this->isCompatibleWithSharedFileSystem();
        return ($storage || $compute || $platform || $dns || $orchestration || $sharedFS) && !$this->isOpenStackPoweredExpired();
    }

    /**
     * @return bool
     */
    public function isOpenStackTested():bool
    {
        $program_version = $this->ProgramVersion();
        return !is_null($program_version) && $program_version->ID > 0;
    }

    /**
     * @return string
     */
    public function getTestedCapabilityTypeLabel():string
    {
        $components = [];
        if ($this->isCompatibleWithPlatform()) {
            $components[]= 'Platform';
        }
        if ($this->isCompatibleWithCompute()) {
            $components[]=  'Compute';
        }
        if ($this->isCompatibleWithStorage()) {
            $components[]=  'Storage';
        }
        if ($this->isCompatibleWithDNS()) {
            $components[]= 'DNS';
        }
        if ($this->isCompatibleWithOrchestration()) {
            $components[]= 'Orchestration';
        }
        if ($this->isCompatibleWithSharedFileSystem()) {
            $components[]= 'Shared File System';
        }
        return implode(", ", $components);
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
        $program_version = $this->ProgramVersion();
        UnitOfWork::getInstance()->scheduleForUpdate($program_version);
        return $program_version;
    }

    public function getTestedCapabilities()
    {
        $program_type = '';
        if ($this->isCompatibleWithPlatform()) {
            $program_type = '';
        } else if ($this->isCompatibleWithCompute()) {
            $program_type = 'OpenStack Powered Compute';
        } else if ($this->isCompatibleWithStorage()) {
            $program_type = 'OpenStack Powered Object Storage';
        }
        else if ($this->isCompatibleWithDNS()) {
            $program_type = 'OpenStack Powered DNS';
        }
        else if ($this->isCompatibleWithOrchestration()) {
            $program_type = 'OpenStack Powered Orchestration';
        }
        else if ($this->isCompatibleWithSharedFileSystem()) {
            $program_type = 'OpenStack Powered Shared File System';
        }
        return $this->getProgramVersion()->getCapabilitiesByProgramType($program_type);
    }

    public function getDesignatedSections()
    {
        $program_type = '';
        if ($this->isCompatibleWithPlatform()) {
            $program_type = '';
        } else if ($this->isCompatibleWithCompute()) {
            $program_type = 'OpenStack Powered Compute';
        } else if ($this->isCompatibleWithStorage()) {
            $program_type = 'OpenStack Powered Object Storage';
        }
        else if ($this->isCompatibleWithDNS()) {
            $program_type = 'OpenStack Powered DNS';
        }
        else if ($this->isCompatibleWithOrchestration()) {
            $program_type = 'OpenStack Powered Orchestration';
        }
        else if ($this->isCompatibleWithSharedFileSystem()) {
            $program_type = 'OpenStack Powered Shared File System';
        }

        return $this->getProgramVersion()->getDesignatedSectionsByProgramType($program_type);
    }

    /**
     * @return bool
     */
    public function isOpenStackPoweredExpired()
    {
        $res = false;
        if (!$this->ExpiryDate) return $res;
        $utc_timezone = new \DateTimeZone("UTC");
        $time_zone = new \DateTimeZone('America/Chicago');
        $expiry_date = new \DateTime($this->ExpiryDate, $time_zone);
        $expiry_date = $expiry_date->setTimezone($utc_timezone);
        $utc_now = new \DateTime(null, new \DateTimeZone("UTC"));

        return $utc_now > $expiry_date;
    }

    /**
     * @return string
     */
    public function getPrintableZenDeskLinks()
    {
        $list = [];
        foreach ($this->ZenDeskLinks() as $link) {
            $list[] = $link->Link;
        }

        return join('|', $list);
    }

    /**
     * @return string
     */
    public function getPrintableRefStackLinks()
    {
        $list = [];
        foreach ($this->RefStackLinks() as $link) {
            $list[] = $link->Link;
        }

        return join('|', $list);
    }
}