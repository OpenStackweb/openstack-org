<?php

/**
 * Copyright 2015 OpenStack Foundation
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
class PresentationMaterial extends DataObject
{
    private static $db = array
    (
        'Name'          => 'Text',
        'Description'   => 'HTMLText',
        'DisplayOnSite' => 'Boolean',
        'Featured'      => 'Boolean',
        'Order'         => 'Int',
    );

    private static $has_many = array
    (
    );

    private static $defaults = array
    (
    );

    private static $many_many = array
    (
    );

    static $many_many_extraFields = array(
    );

    private static $has_one = array
    (
        'Presentation' => 'Presentation',
    );

    private static $summary_fields = array
    (
        'Name'      => 'Presentation Name',
        'ClassName' => 'Type'
    );

    private static $searchable_fields = array
    (
        'Name',
        'ClassName'
    );

    public function getCMSFields()
    {

        $f = new FieldList
        (
            $rootTab = new TabSet("Root", $tabMain = new Tab('Main'))
        );

        $f->addFieldToTab('Root.Main', new TextField('Name','Name'));
        $f->addFieldToTab('Root.Main', new HtmlEditorField('Description','Description'));
        $f->addFieldToTab('Root.Main', new CheckboxField('DisplayOnSite','Display On Site'));
        $f->addFieldToTab('Root.Main', new CheckboxField('Featured','Is Featured'));

        return $f;
    }

    /**
     * @param Member $member
     * @return boolean
     */
    public function canView($member = null) {
        return Permission::check("ADMIN") || Permission::check("ADMIN_SUMMIT_APP") || Permission::check("ADMIN_SUMMIT_APP_SCHEDULE");
    }

    /**
     * @param Member $member
     * @return boolean
     */
    public function canEdit($member = null) {
        return Permission::check("ADMIN") || Permission::check("ADMIN_SUMMIT_APP") || Permission::check("ADMIN_SUMMIT_APP_SCHEDULE");
    }

    /**
     * @return ValidationResult
     */
    protected function validate()
    {
        $valid = parent::validate();

        if (!$valid->valid()) {
            return $valid;
        }

        $name = trim($this->Name);

        if (empty($name)) {
            return $valid->error('Name is mandatory!');
        }

        return $valid;
    }

    public function getTitle() {
        return ($this->Name) ? $this->Name : $this->Presentation()->Title;
    }

    public function getDescription() {
        return ($this->getField('Description')) ? $this->getField('Description') : $this->Presentation()->ShortDescription;
    }

}