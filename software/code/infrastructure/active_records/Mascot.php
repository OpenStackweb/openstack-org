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
 * Class Mascot
 */
class Mascot extends DataObject implements IMascot
{

    private static $create_table_options = array('MySQLDatabase' => 'ENGINE=InnoDB');

    private static $db = array
    (
        'Name' => 'Varchar(255)',
        'CodeName' => 'Varchar(255)',
        'Hide' => 'Boolean',
    );

    private static $belongs_to = array
    (
        "OpenStackComponent" => "OpenStackComponent.Mascot"
    );

    private static $summary_fields = array
    (
        'Name' => 'Name',
        'CodeName' => 'Code Name',
        'OpenStackComponent.CodeName' => 'Component',
        'Hide' => 'Hide',
    );

    static $mascots_dir = 'images/project-mascots';

    /**
     * @return int
     */
    public function getIdentifier()
    {
        return (int)$this->getField('ID');
    }

    /**
     * @param FieldList $fields
     * @return FieldList|void
     */
    public function updateCMSFields(FieldList $fields)
    {
        $oldFields = $fields->toArray();
        foreach ($oldFields as $field) {
            $fields->remove($field);
        }

        $fields->push(new LiteralField("Title", "<h2>OpenStack Component Mascot</h2>"));
        $fields->push(new TextField("Name", "Name"));
        $fields->push(new TextField("CodeName", "Code Name"));
        $fields->push(new CheckboxField('Hide', 'Hide Mascot?'));

        return $fields;
    }

    public function getCodeName()
    {
        if ($this->OpenStackComponent()->Exists())
            return $this->OpenStackComponent()->CodeName;
        else if ($this->getField('CodeName'))
            return $this->getField('CodeName');
        else
            return null;
    }

    public function getImageDir()
    {
        if ($this->getCodeName())
            return CloudAssetTemplateHelpers::cloud_url(self::$mascots_dir). $this->getCodeName();
        else
            return null;
    }

    public function getImageLink()
    {
        if ($this->getCodeName())
            return CloudAssetTemplateHelpers::cloud_url(self::$mascots_dir). $this->getCodeName();
        else
            return null;
    }

}