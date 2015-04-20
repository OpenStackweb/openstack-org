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
        "Program" => "InteropProgramType",
        "Version" => "InteropProgramVersion",
    );


    function getCMSFields()
    {
        $fields =  new FieldList();
        $fields->add(new TextField('Name','Name'));
        $fields->add(new HtmlEditorField('Comment','Comment'));
        $fields->add(new HtmlEditorField('Guidance','Guidance'));
        $fields->add(new DropdownField('Status','Status', $this->dbObject('Status')->enumValues()));
        $fields->add(new DropdownField('Program','Program',   InteropProgramType::get()->filter('HasCapabilities', true)->map("ID", "Name", "Please Select")));
        $fields->add(new DropdownField('Version','Program Version', Dataobject::get("InteropProgramVersion")->map("ID", "Name", "Please Select")));
        return $fields;
    }
}