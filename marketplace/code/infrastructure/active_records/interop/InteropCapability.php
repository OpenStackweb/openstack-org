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

class InteropCapability extends DataObject {

    static $db = array(
        'Name'  => 'Varchar',
        'Order' => 'Int',
        'Description' => 'HTMLText',
        'Status' =>  "Enum('Required, Advisory','Required')",
    );

    private static $has_one = array(
        "Program" => "InteropProgramType",
        "Version" => "InteropProgramVersion",
    );

    function getCMSFields()
    {
        $fields =  new FieldList();
        $fields->add(new TextField('Name','Name'));
        $fields->add(new HtmlEditorField('Description','Description'));
        $fields->add(new DropdownField('Status','Status', $this->dbObject('Status')->enumValues()));
        $fields->add($ddl_program = new DropdownField('Program','Program',   InteropProgramType::get()->filter('HasCapabilities', true)->map("ID", "Name", "Please Select")));
        $fields->add($ddl_version = new DropdownField('Version','Program Version', Dataobject::get("InteropProgramVersion")->map("ID", "Name", "Please Select")));

        if($this->ID > 0){
            $ddl_program->setValue($this->ProgramID);
            $ddl_version->setValue($this->VersionID);
        }

        return $fields;
    }
}