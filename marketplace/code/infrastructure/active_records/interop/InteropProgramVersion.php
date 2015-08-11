<?php
/**
 * Copyright 2015 Openstack Foundation
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
 * Class InteropProgramVersion
 */
class InteropProgramVersion extends DataObject implements IInteropProgramVersion {

    static $db = array(
        'Name'  => 'Varchar',
    );

    private static $many_many = array(
        'Capabilities'       => 'InteropCapability',
        'DesignatedSections' => 'InteropDesignatedSection',
    );

    function getCMSFields()
    {
        $fields =  new FieldList();
        $fields->add(new TextField('Name','Name'));
        if($this->ID){
            $config = GridFieldConfig_RelationEditor::create();
            $config->addComponent(new GridFieldSortableRows('Order'));

            $data_columns = $config->getComponentByType('GridFieldDataColumns');
            $data_columns->setDisplayFields(array(
                'Name' => 'Name',
                'Type.Name'=> 'Type',
                'Status' => 'Status'
            ));

            $gridField = new GridField('Capabilities', 'Capabilities', $this->Capabilities(), $config);
            $fields->add($gridField);

            $config = GridFieldConfig_RelationEditor::create();
            $config->addComponent(new GridFieldSortableRows('Order'));

            $data_columns = $config->getComponentByType('GridFieldDataColumns');
            $data_columns->setDisplayFields(array(
                'Name' => 'Name',
                'Guidance' => 'Guidance',
                'Status' => 'Status'
            ));

            $gridField = new GridField('DesignatedSections', 'Designated Sections', $this->DesignatedSections(), $config);
            $fields->add($gridField);
        }
        return $fields;
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
        return (string)$this->getField('Name');
    }

    /**
     * @param string $version_name
     * @return void
     */
    public function setName($version_name)
    {
       $this->setField('Name', $version_name);
    }

    public function getCapabilitiesByProgramType($program_type) {
        if ($program_type) {
            $program_type = InteropProgramType::get('InteropProgramType')->filter('Name',$program_type);
            return $this->Capabilities()->filter('Program.ID', $program_type->First()->ID);
        } else {
            return $this->Capabilities();
        }
    }

    public function getDesignatedSectionsByProgramType($program_type) {
        if ($program_type) {
            $program_type = InteropProgramType::get('InteropProgramType')->filter('Name',$program_type);
            return $this->DesignatedSections()->filter('Program.ID', $program_type->First()->ID);
        } else {
            return $this->DesignatedSections();
        }
    }

}