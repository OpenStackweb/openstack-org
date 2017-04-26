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

    use SluggableEntity;

    private static $create_table_options = array('MySQLDatabase' => 'ENGINE=InnoDB');

    // IMPORTANT : this fixes the order for categories on software page
    public static $categories = array(
        "Compute",
        "Storage, Backup & Recovery",
        "Networking & Content Delivery",
        "Data & Analytics",
        "Security, Identity & Compliance",
        "Management Tools",
        "Deployment Tools",
        "Application Services",
        "None"
    );

    private static $db = array
    (
        'Name'                         => 'Varchar(255)',
        'CodeName'                     => 'Varchar(255)',
        'Description'                  => 'Text',
        'SupportsVersioning'           => 'Boolean',
        'SupportsExtensions'           => 'Boolean',
        'IsCoreService'                => 'Boolean',
        'MascotClass'                  => "Enum('Barbican,Chef OpenStack,Cinder,CLI,CloudKitty,Congress,Designate,Dragonflow,Freezer,Fuel,Glance,Heat,Horizon,Ironic,Karbor,Keystone,Kolla,Kuryr,Magnum,Manila,Mistral,Monasca,Murano,Neutron,Nova,Octavia,OpenStack Charms,OpenStackansible,Puppet OpenStack,Rally,Sahara,Searchlight,Senlin,Solum,Storlets,Swift,Tacker,Telemetry,Tricircle,TripleO,Trove,Vitrage,Watcher,Zun','Barbican')",
        'Use'                          => 'Enum(array("Application Services","Compute","Data & Analytics","Deployment Tools","Management Tools","Monitoring & Metering","Networking & Content Delivery","Security, Identity & Compliance","Storage, Backup & Recovery","None"), "None")',
        'HasStableBranches'            => 'Boolean',
        'WikiUrl'                      => 'Text',
        'TCApprovedRelease'            => 'Boolean',
        'HasTeamDiversity'             => 'Boolean',
        'IncludedComputeStarterKit'    => 'Boolean',
        'VulnerabilityManaged'         => 'Boolean',
        'Order'                        => 'Int',
        'YouTubeID'                    => 'Varchar',
        'VideoDescription'             => 'Text',
        'VideoTitle'                   => 'Varchar',
        'FollowsStandardDeprecation'   => 'Boolean',
        'SupportsUpgrade'              => 'Boolean',
        'SupportsRollingUpgrade'       => 'Boolean',
        'ShowOnMarketplace'            => 'Boolean(1)',
        'Slug'                         => 'Varchar(255)'
    );

    private static $has_one = array
    (
        "LatestReleasePTL" => "Member"
    );

    private static $has_many = array
    (
        'Versions'       => 'OpenStackApiVersion',
        'RelatedContent' => 'OpenStackComponentRelatedContent',
        'Caveats'        => 'OpenStackComponentReleaseCaveat',
    );


    private static $belongs_many_many = array
    (
        "Releases" => "OpenStackRelease",
    );

    private static $indexes = array
    (
        'Name'     => array('type' => 'index', 'value' => 'Name'),
        'CodeName' => array('type' => 'index', 'value' => 'CodeName'),
        'NameCodeName' => array(
            'type' => 'unique',
            'value' => '"Name","CodeName"'
        ),
        'Slug' => array(
            'type' => 'unique',
            'value' => 'Slug'
        )
    );

    private static $defaults = array
    (
        'MascotClass'       => 'Barbican',
        'ShowOnMarketplace' => 1,
    );

    protected function onBeforeWrite()
    {
        parent::onBeforeWrite();
        $this->Slug = $this->generateSlug($this->CodeName);
        if($this->SupportsVersioning){
            // delete all dummy records
            DB::query("DELETE FROM OpenStackReleaseSupportedApiVersion WHERE OpenStackComponentID = {$this->ID} AND ApiVersionID = 0 ;");
        }
    }

    protected function onBeforeDelete() {
        parent::onBeforeDelete();
        // clean related do so no leave orphans
        // one to many relations
        foreach($this->Versions() as $item){
            $item->delete();
        }
        foreach($this->RelatedContent() as $item){
            $item->delete();
        }
        foreach($this->Caveats() as $item){
            $item->delete();
        }
        // many many relation
        $this->Releases()->removeAll();
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

    public function getMascotRef()
    {
        return str_replace(' ', '-', strtolower($this->getCodeName()));
    }

    public function setCodeName($codename)
    {
        $this->setField('CodeName', $codename);
    }

    public function getSlug(){
        return $this->getField('Slug');
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
            throw new Exception('Component does not support api versioning');
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
            throw new Exception('Component does not support api versioning');
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
     * @param string $version
     * @return OpenStackApiVersion
     */
    public function getVersionByLabel($version)
    {
        foreach ($this->getVersions() as $api_version) {
            if ($api_version->Version == $version) {
                return $api_version;
            }
        }

        return null;
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

    public function getCaveatsForReleaseType($release_id, $type)
    {
        return OpenStackComponentReleaseCaveat::get()->filter
        (
            array
            (
                'ReleaseID'   => intval($release_id),
                'Type'        => $type,
                'ComponentID' => $this->ID
            )
        );
    }
}