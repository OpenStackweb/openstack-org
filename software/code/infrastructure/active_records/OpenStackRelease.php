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
 * Class OpenStackRelease
 */
class OpenStackRelease
    extends DataObject
    implements IOpenStackRelease
{

    static $create_table_options = array('MySQLDatabase' => 'ENGINE=InnoDB');

    static $db = array
    (
        'Name'                             => 'Varchar',
        'ReleaseNumber'                    => 'Varchar',
        'ReleaseDate'                      => 'Date',
        'ReleaseNotesUrl'                  => 'Text',
        'Status'                           => "Enum('Deprecated, EOL, SecuritySupported , Current, UnderDevelopment, Future', 'Deprecated')",
        'HasStatistics'                    => 'Boolean',
    );

    static $summary_fields = array
    (
        'Name'          => 'Name',
        'ReleaseNumber' => 'Release Number',
        'ReleaseDate'   => 'Release Date',
        'Status'        => 'Status',
    );


    static $indexes = array
    (
        'Name'          => array('type' => 'unique', 'value' => 'Name'),
        'ReleaseNumber' => array('type' => 'unique', 'value' => 'ReleaseNumber'),
        'ReleaseDate'   => array('type' => 'unique', 'value' => 'ReleaseDate'),
    );

    static $many_many = array
    (
        'OpenStackComponents' => 'OpenStackComponent',
    );

    private static $many_many_extraFields = array
    (
        'OpenStackComponents'  => array
                                (
                                    'Adoption'                               => 'Int',
                                    'MaturityPoints'                         => 'Int',
                                    'HasInstallationGuide'                   => 'Boolean',
                                    'SDKSupport'                             => 'Int',
                                    'QualityOfPackages'                      => 'Text',
                                    'MostActiveContributorsByCompanyJson'    => 'Text',
                                    'MostActiveContributorsByIndividualJson' => 'Text',
                                    'ContributionsJson'                      => 'Text',
                                )
    );

    static $has_many = array
    (
        'SupportedApiVersions'     => 'OpenStackReleaseSupportedApiVersion',
        'SampleConfigurationTypes' => 'OpenStackSampleConfigurationType',
        'Caveats'                  => 'OpenStackComponentReleaseCaveat',
    );


    protected function onBeforeDelete() {
        parent::onBeforeDelete();
        foreach($this->SupportedApiVersions() as $item){
            $item->delete();
        }
        foreach($this->SampleConfigurationTypes() as $item){
            $item->delete();
        }
        foreach($this->Caveats() as $item){
            $item->delete();
        }
        $this->OpenStackComponents()->removeAll();
    }

    /**
     * @return int
     */
    public function getIdentifier()
    {
        return (int)$this->getField('ID');
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->getField('Name');
    }

    /**
     * @param string $name
     * @return void
     */
    public function setName($name)
    {
        $this->setField('Name', $name);
    }

    public function getSlug()
    {
        return strtolower($this->getName());
    }

    /**
     * @return string
     */
    public function getReleaseNumber()
    {
        return $this->getField('ReleaseNumber');
    }

    /**
     * @param string $release_number
     * @return void
     */
    public function setReleaseNumber($release_number)
    {
        $this->setField('ReleaseNumber', $release_number);
    }

    /**
     * @param bool $raw
     * @return DateTime|string
     */
    public function getReleaseDate($raw = true)
    {
        $date = $this->getField('ReleaseDate');
        if ($raw) {
            return $date;
        }
        $date_time = new DateTime($date);

        return $date_time;
    }

    /**
     * @param DateTime $release_date
     * @return void
     */
    public function setReleaseDate(DateTime $release_date)
    {
        if (is_string($release_date)) {
            $this->setField('ReleaseDate', $release_date);
        } else {
            $this->setField('ReleaseDate', $release_date->format('Y-m-d'));
        }
    }

    /**
     * @return string
     */
    public function getReleaseNotesUrl()
    {
        return $this->getField('ReleaseNotesUrl');
    }

    /**
     * @param string $release_notes_url
     * @return void
     */
    public function setReleaseNotesUrl($release_notes_url)
    {
        $this->setField('ReleaseNotesUrl', $release_notes_url);
    }


    /**
     * @return IOpenStackComponent[]
     */
    public function getOpenStackComponents()
    {
        return AssociationFactory::getInstance()->getMany2ManyAssociation($this, 'OpenStackComponents')->toArray();
    }

    /**
     * @param IOpenStackComponent $new_component
     * @return void
     */
    public function addOpenStackComponent(IOpenStackComponent $new_component)
    {
        AssociationFactory::getInstance()->getMany2ManyAssociation($this, 'OpenStackComponents')->add($new_component);
    }


    /**
     * @param int $component_id
     * @return IOpenStackComponent
     */
    public function getOpenStackComponent($component_id)
    {
        $list = $this->getOpenStackComponents();
        foreach ($list as $entity) {
            if ($entity->getIdentifier() == $component_id) {
                return $entity;
            }
        }

        return false;
    }

    /**
     * @param IOpenStackApiVersion $version
     * @return void
     */
    public function addSupportedVersion(IOpenStackApiVersion $version)
    {
        if ($this->supportsComponent($version->getReleaseComponent()->getCodeName())) {
            //add supported version
            $new_supported_version = new OpenStackReleaseSupportedApiVersion;
            $new_supported_version->OpenStackComponentID = $version->getReleaseComponent()->getIdentifier();
            $new_supported_version->ReleaseID = $this->getIdentifier();
            $new_supported_version->ApiVersionID = $version->getIdentifier();

            AssociationFactory::getInstance()->getOne2ManyAssociation($this,
                'SupportedApiVersions')->add($new_supported_version);
        }

        return false;
    }

    /**
     * @param string $code_name
     * @return bool
     */
    public function supportsComponent($code_name)
    {
        $res = $this->getManyManyComponents('OpenStackComponents',
            "OpenStackComponent.CodeName = '{$code_name}' ")->First();

        return $res;
    }

    /**
     * @return array|IOpenStackApiVersion[]
     */
    public function getSupportedApiVersions()
    {
        return AssociationFactory::getInstance()->getOne2ManyAssociation($this, 'SupportedApiVersions')->toArray();
    }

    /**
     * @param IOpenStackApiVersion $version
     * @return IReleaseSupportedApiVersion
     */
    public function supportsApiVersion(IOpenStackApiVersion $version)
    {
        $version_id = $version->getIdentifier();
        $component_id = $version->getReleaseComponent()->getIdentifier();

        return $this->getComponents('SupportedApiVersions',
            "ApiVersionID = {$version_id} AND  OpenStackComponentID = {$component_id} ")->First();
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->getField('Status');
    }

    public function getStatusI18n()
    {
    	return _t('Software.RELEASE_STATUS_'.strtoupper($this->Status), $this->Status);
    }

    /**
     * @param string $status
     * @return void
     */
    public function setStatus($status)
    {
        $this->setField('Status', $status);
    }
    /**
     * @param string $term
     * @param int $adoption
     * @param int $maturity
     * @param int $age
     * @return IOpenStackComponent[]
     */
    public function getOpenStackCoreComponents($term = '', $adoption = 0, $maturity = 0, $age = 0)
    {
        $filters = array
        (
            'IsCoreService'                     => true,
        );

        $query = $this->OpenStackComponents()->filter($filters);

        $query = $query->where(" Adoption >= {$adoption} AND  MaturityPoints >= {$maturity}");

        if(!empty($term))
        {
            $query = $query->where(" (Name LIKE '%{$term}%' OR CodeName LIKE '%{$term}%' OR Description LIKE '%{$term}%' ) ");
        }

        $final = array();
        $res   = $query->sort('ID','ASC')->toArray();
        foreach($res as $c)
        {
            if($c->getAge() >= $age)
                array_push($final, $c);
        }
        return $final;
    }

    public function getOpenStackCoreComponentsCount()
    {
        return $this->OpenStackComponents()->filter('IsCoreService', true)->count();
    }

    public function getOpenStackOptionalComponentsCount()
    {
        return $this->OpenStackComponents()->filter('IsCoreService', false)->count();
    }

    /**
     * @param string $term
     * @param int $adoption
     * @param int $maturity
     * @param int $age
     * @param string $sort
     * @param string $sort_dir
     * @return IOpenStackComponent[]
     */
    public function getOpenStackOptionalComponents($term = '', $adoption = 0, $maturity = 0, $age = 0, $sort = '', $sort_dir ='')
    {
        $filters = array
        (
            'IsCoreService'                     => false,
        );

        $query = $this->OpenStackComponents()->filter($filters);

        $query = $query->where(" Adoption >= {$adoption} AND  MaturityPoints >= {$maturity}");

        if(!empty($term))
        {
            $query = $query->where(" (Name LIKE '%{$term}%' OR CodeName LIKE '%{$term}%' OR Description LIKE '%{$term}%' ) ");
        }
        $final = array();
        if(!empty($sort) && ( $sort === 'maturity' || $sort === 'adoption'))
        {
            $sort  = $sort === 'maturity' ? 'MaturityPoints' : 'Adoption';
            $query = $query->sort($sort, $sort_dir);
        }
        $res   = $query->toArray();
        foreach($res as $c)
        {
            if($c->getAge() >= $age)
                array_push($final, $c);
        }
        if(!empty($sort) &&  $sort === 'age')
        {
            usort($final, function ($a, $b) use($sort_dir) {
                if($a->getAge() === $b->getAge()) return 0;
                if($sort_dir === 'asc'){
                    return $a->getAge() > $b->getAge() ? 1 : -1;
                }
                else
                {
                    return $a->getAge() < $b->getAge() ? 1 : -1;
                }
            });
        }
        return $final;
    }

    /**
     * @param int $component_id
     * @return IOpenStackComponent
     */
    public function getComponentById($component_id)
    {
        return $this->OpenStackComponents()->filter(array( 'OpenStackComponentID' => $component_id))->first();
    }

    public function getVersionLabel($component_id)
    {
        $api = $this->SupportedApiVersions()
            ->innerJoin('OpenStackApiVersion','OpenStackApiVersion.ID = OpenStackReleaseSupportedApiVersion.ApiVersionID')
            ->filter('OpenStackComponentID', $component_id)->sort('OpenStackReleaseSupportedApiVersion.ReleaseVersion','DESC')->first();
        if(is_null($api))
            return 'N/A';
        $res = $api->ReleaseVersion;
        if(empty($res))
            $res = $api->ApiVersion()->Version;
        return $res;
    }

    public function getDefaultSampleConfigurationType()
    {
        return $this->SampleConfigurationTypes()->filter('IsDefault', true)->first();
    }

    public function getComponentAdoption($component_id)
    {
        $component = $this->getComponentById($component_id);
        if(is_null($component)) return null;
        return $component->Adoption;
    }

    public function getComponentMaturityPoints($component_id)
    {
        $component = $this->getComponentById($component_id);
        if(is_null($component)) return null;
        return $component->MaturityPoints;
    }
}