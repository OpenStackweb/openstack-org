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

class InteropDesignatedSection extends DataObject {

    static $db = array(
        'Name'  => 'Varchar',
        'Order' => 'Int',
        'Comment' => 'HTMLText',
        'Guidance' => 'HTMLText',
        'Status' =>  "Enum('Required, Advisory, Deprecated, Removed, Informational','Required')",
    );

    private static $has_one = array(
        "Version" => "InteropProgramVersion"
    );

    private static $many_many = array(
        "Program" => "InteropProgramType"
    );


    function getCMSFields()
    {
        $fields =  new FieldList();
        $fields->add(new TextField('Name','Name'));
        $fields->add(new HtmlEditorField('Comment','Comment'));
        $fields->add(new HtmlEditorField('Guidance','Guidance'));
        $fields->add(new DropdownField('Status','Status', $this->dbObject('Status')->enumValues()));
        $fields->add($ddl_program = new CheckboxsetField('Program','Program', InteropProgramType::get()->filter('HasCapabilities', true)->sort('Order')->map("ID", "ShortName")));
        $fields->add($ddl_version = new DropdownField('VersionID','Program Version', Dataobject::get("InteropProgramVersion")->map("ID", "Name", "Please Select")));

        if($this->ID > 0){
            $ddl_program->setValue('ID',$this->Program());
            $ddl_version->setValue($this->VersionID);
        }

        return $fields;
    }

    function isCompute() {
        $programs = $this->Program();
        foreach ($programs as $program) {
            if ($program->ShortName == 'Compute') return true;
        }

        return false;
    }

    function isStorage() {
        $programs = $this->Program();
        foreach ($programs as $program) {
            if ($program->ShortName == 'Storage') return true;
        }

        return false;
    }

    function isPlatform() {
        $programs = $this->Program();
        foreach ($programs as $program) {
            if ($program->ShortName == 'Platform') return true;
        }

        return false;
    }
}