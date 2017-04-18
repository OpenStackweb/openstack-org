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
 * Class OpenStackApiVersion
 */
class OpenStackApiVersion extends DataObject implements IOpenStackApiVersion
{

    static $create_table_options = array('MySQLDatabase' => 'ENGINE=InnoDB');

    static $db = array
    (
        'Version'               => 'Varchar',
        /* @deprecated use OpenStackReleaseSupportedApiVersion.Status instead */
        'Status'                => "Enum('Deprecated, Supported, Current, Beta, Alpha' , 'Deprecated')",
        'CreatedFromTask'       => 'Boolean',
    );

    /**
     * @param string $external_status
     * @return string
     */
    public static function convertStatus($external_status){
        switch ($external_status){
            case "DEPRECATED": return 'Deprecated';
            case "SUPPORTED": return 'Supported';
            case "CURRENT": return 'Current';
        }
        return "Deprecated";
    }

    static $summary_fields = array(
        'Version'         => 'Version',
        /* @deprecated use OpenStackReleaseSupportedApiVersion.Status instead */
        'Status'          => 'Status',
        'CreatedFromTask' => 'CreatedFromTask',
    );

    static $indexes = array(
        'Version_Component' => array('type' => 'unique', 'value' => 'Version,OpenStackComponentID'),
    );

    static $has_one = array(
        'OpenStackComponent' => 'OpenStackComponent',
    );

    private static $has_many = array(
        'OpenStackReleaseSupportedApiVersions' => 'OpenStackReleaseSupportedApiVersion',
    );

    protected function onBeforeDelete() {
        parent::onBeforeDelete();
        // one to many relations
        foreach ($this->OpenStackReleaseSupportedApiVersions() as $item){
            $item->delete();
        }
    }

    protected function onBeforeWrite()
    {
        parent::onBeforeWrite();
        $this->OpenStackComponentID = $this->getReleaseComponent()->getIdentifier();
    }

    /**
     * @return int
     */
    public function getIdentifier()
    {
        return (int)$this->getField('ID');
    }

    /**
     * @param string $version
     * @return void
     */
    public function setVersion($version)
    {
        $this->setField('Version', $version);
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->getField('Version');
    }


    /**
     * @return IOpenStackComponent
     */
    public function getReleaseComponent()
    {
        return AssociationFactory::getInstance()->getMany2OneAssociation($this, 'OpenStackComponent',
            'Versions')->getTarget();
    }

    /**
     * @param IOpenStackComponent $new_component
     * @return void
     */
    public function setReleaseComponent(IOpenStackComponent $new_component)
    {
        AssociationFactory::getInstance()->getMany2OneAssociation($this, 'OpenStackComponent',
            'Versions')->setTarget($new_component);
    }

    /**
     * @deprecated use OpenStackReleaseSupportedApiVersion.Status
     * @return string
     */
    public function getStatus()
    {
        return $this->getField('Status');
    }

    /**
     * @deprecated use OpenStackReleaseSupportedApiVersion.Status instead
     * @param string $status
     * @return void
     */
    public function setStatus($status)
    {
        $this->setField('Status', $status);
    }

    /**
     * @deprecated use OpenStackReleaseSupportedApiVersion.Status instead
     * @return string
     */
    public function getStatusI18n()
    { 
    	return _t('Software.API_VERSION_STATUS_'.strtoupper($this->Status), $this->Status);
    }
}