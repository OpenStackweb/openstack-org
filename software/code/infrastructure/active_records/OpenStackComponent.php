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

    static $db = array
    (
        'Name'                         => 'Varchar(255)',
        'CodeName'                     => 'Varchar(255)',
        'ProjectTeam'                  => 'Varchar(255)',
        'Description'                  => 'Text',
        'SupportsVersioning'           => 'Boolean',
        'SupportsExtensions'           => 'Boolean',
        'IsCoreService'                => 'Boolean',
        'WikiUrl'                      => 'Text',
        'Order'                        => 'Int',
        'YouTubeID'                    => 'Varchar',
        'VideoDescription'             => 'Text',
        'VideoTitle'                   => 'Varchar',
        'ShowOnMarketplace'            => 'Boolean(1)',
        'Slug'                         => 'Varchar(255)',
        'Since'                        => 'Varchar(255)'
    );

    static $has_one = array
    (
        "LatestReleasePTL"  => "Member",
        "Mascot"            => "Mascot",
        "Category"          => "OpenStackComponentCategory",
        "DocsLink"          => "OpenStackComponentLink",
        "DownloadLink"      => "OpenStackComponentLink"
    );

    static $has_many = array
    (
        'Versions'          => 'OpenStackApiVersion',
        'RelatedContent'    => 'OpenStackComponentRelatedContent',
        'Caveats'           => 'OpenStackComponentReleaseCaveat',
        'Links'             => 'OpenStackComponentLink.Links',
    );

    static $many_many = array
    (
        'Tags'              => 'OpenStackComponentTag',
        'Dependencies'      => 'OpenStackComponent',
        'RelatedComponents' => 'OpenStackComponent',
        'SupportTeams'      => 'OpenStackComponent',
        'CapabilityTags'    => 'OpenStackComponentCapabilityTag'
    );

    private static $many_many_extraFields = array
    (
        'Tags' => array( 'SortOrder' => 'Int' )
    );

    static $belongs_many_many = array
    (
        "Releases" => "OpenStackRelease",
    );

    static $indexes = array
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

    static $defaults = array
    (
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
        $mascotName = $this->Mascot()->Exists() ? strtolower($this->Mascot()->CodeName) : 'barbican';
        return str_replace(' ', '-', $mascotName);
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

    /**
     * @param IOpenStackComponentTag $new_tag
     * @return void
     */
    public function addTag(IOpenStackComponentTag $new_tag)
    {
        AssociationFactory::getInstance()->getMany2ManyAssociation($this, 'Tags')->add($new_tag);
    }

    /**
     * @return IOpenStackComponentTag[]
     */
    public function getMaturityTags()
    {
        return $this->Tags()->filter('Type', 'maturity');
    }

    /**
     * @return IOpenStackComponentTag[]
     */
    public function getInfoTags()
    {
        return $this->Tags()->filter('Type', 'info');
    }

    public function getParentCategory() {
        if ($this->Category()->Exists()) {
            return $this->Category()->getParentCategory();
        } else {
            return null;
        }
    }

    public function getLink() {
        $default_release = OpenStackRelease::getDefaultRelease();
        $software_page = SoftwareHomePage::get()->first();

        return $software_page->Link().'releases/'.strtolower($default_release->Name).'/components/'.$this->Slug;
    }

    public function hasCodeLink() {
        $url = $this->getCodeLink();
        $headers = @get_headers($url);
        return (strpos($headers[0],'404') === false);
    }

    public function getCodeLink() {
        $release = strtolower(OpenStackRelease::getDefaultRelease()->Name);
        $slug = ($this->ProjectTeam) ? $this->generateSlug($this->ProjectTeam) : $this->Slug;
        $slug = str_replace('-','_', $slug);
        return 'https://releases.openstack.org/teams/'.$slug.'.html#team-'.$release.'-'.$slug;
    }

    public function getProjectLink() {
        $slug = ($this->ProjectTeam) ? $this->generateSlug($this->ProjectTeam) : $this->Slug;

        return 'https://governance.openstack.org/tc/reference/projects/'.$slug.'.html';
    }
}