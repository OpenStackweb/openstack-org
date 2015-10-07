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
 * Class OpenStackComponent
 */
class OpenStackComponent extends DataObject implements IOpenStackComponent
{

    private static $create_table_options = array('MySQLDatabase' => 'ENGINE=InnoDB');

    private static $db = array
    (
        'Name'                         => 'Varchar',
        'CodeName'                     => 'Varchar',
        'Description'                  => 'Text',
        'SupportsVersioning'           => 'Boolean',
        'SupportsExtensions'           => 'Boolean',
        'IsCoreService'                => 'Boolean',
        'IconClass'                    => 'Text',
        'Use'                          => 'Enum(array("Compute","Object Storage","None"), "None")',
        'HasStableBranches'            => 'Boolean',
        'WikiUrl'                      => 'Text',
        'TCApprovedRelease'            => 'Boolean',
        'ReleaseMileStones'            => 'Boolean',
        'ReleaseCycleWithIntermediary' => 'Boolean',
        'ReleaseIndependent'           => 'Boolean',
        'HasTeamDiversity'             => 'Boolean',
    );

    private static $has_one = array
    (
        "LatestReleasePTL" => "Member"
    );

    private static $has_many = array
    (
        'Versions' => 'OpenStackApiVersion',
    );

    private static $belongs_many_many = array
    (
        "Releases" => "OpenStackRelease",
    );

    private static $indexes = array
    (
        'Name'     => array('type' => 'unique', 'value' => 'Name'),
        'CodeName' => array('type' => 'unique', 'value' => 'CodeName')
    );

    private static $defaults = array
    (
        'IconClass' => 'fa-cogs',
    );

    protected function onBeforeWrite()
    {
        parent::onBeforeWrite();
    }

    /**
     * @return int
     */
    public function getIdentifier()
    {
        return (int)$this->getField('ID');
    }

    public function getName()
    {
        return $this->getField('Name');
    }

    public function setName($name)
    {
        $this->setField('Name', $name);
    }

    public function getCodeName()
    {
        return $this->getField('CodeName');
    }

    public function getSlug()
    {
        return strtolower($this->getCodeName());
    }


    public function setCodeName($codename)
    {
        $this->setField('CodeName', $codename);
    }

    public function getDescription()
    {
        return $this->getField('Description');
    }

    public function setDescription($description)
    {
        $this->setField('Description', $description);
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getVersions()
    {
        if (!$this->getSupportsVersioning()) {
            throw new Exception('Component does not supports api versioning');
        }

        return AssociationFactory::getInstance()->getOne2ManyAssociation($this, 'Versions')->toArray();
    }

    /**
     * @param IOpenStackApiVersion $new_version
     * @throws Exception
     */
    public function addVersion(IOpenStackApiVersion $new_version)
    {
        if (!$this->getSupportsVersioning()) {
            throw new Exception('Component does not supports api versioning');
        }
        AssociationFactory::getInstance()->getOne2ManyAssociation($this, 'Versions')->add($new_version);
    }

    /**
     * @param int $version_id
     * @return bool
     */
    public function hasVersion($version_id)
    {
        foreach ($this->getVersions() as $version) {
            if ($version->getIdentifier() == $version_id) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return IOpenStackRelease[]
     */
    public function getSupportedReleases()
    {
        return $this->getManyManyComponents('Releases', '', 'OpenStackRelease.ReleaseDate')->toArray();
    }

    public function getSupportsVersioning()
    {
        return (bool)$this->getField('SupportsVersioning');
    }

    public function setSupportsVersioning($supports_versioning)
    {
        $this->setField('SupportsVersioning', $supports_versioning);
    }

    public function clearVersions()
    {
        AssociationFactory::getInstance()->getOne2ManyAssociation($this, 'Versions')->removeAll();
    }

    /**
     * @return bool
     */
    public function getSupportsExtensions()
    {
        return (bool)$this->getField('SupportsExtensions');
    }

    /**
     * @param bool $supports_extensions
     * @return void
     */
    public function setSupportsExtensions($supports_extensions)
    {
        $this->setField('SupportsExtensions', $supports_extensions);
    }

    /**
     * @param string $unit
     * @return int
     */
    public function getAge($unit = 'Yrs')
    {
        $older_release = $this->Releases()->filter('Name:ExactMatch:not','Trunk')->sort('ReleaseDate','ASC')->first();
        if(is_null($older_release)) return 0;
        $date = $older_release->ReleaseDate;
        if(empty($date)) return 0;
        $now  = new DateTime();
        $date = new DateTime($date);
        $res = $now->diff($date)->format("%a");
        return ceil($res / 365.00);
    }

    public function getInstallationGuideDocName()
    {
        return sprintf('%s-install.html', strtolower($this->CodeName));
    }

}