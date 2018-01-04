<?php
/**
 * Copyright 2017 Openstack Foundation
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
 * Class OpenStackComponentTag
 */
class OpenStackComponentTag extends DataObject implements IOpenStackComponentTag
{

    static $db = array
    (
        'Name'                      => 'Varchar(255)',
        'Type'                      => "Enum('maturity, info', 'maturity')",
        'Label'                     => 'Varchar(255)',
        'Description'               => 'Text',
        'Link'                      => 'Varchar(255)',
        'LabelTranslationKey'       => 'Varchar(255)',
        'DescriptionTranslationKey' => 'Varchar(255)',
        'Enabled'                   => 'Boolean(1)'
    );

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

    public function getType()
    {
        return $this->getField('Type');
    }

    public function setType($type)
    {
        $this->setField('Type', $type);
    }

    public function getTranslatedLabel()
    {
        return _t('Software.'.strtoupper($this->LabelTranslationKey), $this->Label);
    }

    public function getTranslatedDescription()
    {
        return _t('Software.'.strtoupper($this->DescriptionTranslationKey), $this->Description);
    }



}