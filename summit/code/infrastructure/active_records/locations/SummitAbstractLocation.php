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
class SummitAbstractLocation extends DataObject implements ISummitLocation
{
    private static $db = array
    (
        'Name'         => 'Varchar(255)',
        'Description'  => 'HTMLText',
        'Order'        => 'Int',
        'LocationType' => 'Enum(array("External","Internal", "None"), "None")',
    );

    private static $has_many = array
    (
    );

    public function getFullName()
    {
        return $this->Name;
    }


    private static $has_one = array
    (
        'Summit' => 'Summit'
    );

    private static $summary_fields = array
    (
        'Name',
        'ClassName'
    );

    private static $searchable_fields = array
    (
        'Name',
        'ClassName',
    );

    public function getTypeName()
    {
        return 'SummitLocation';
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
     * @return string
     */
    public function getDescription()
    {
        return $this->getField('Description');
    }

    public function getCMSFields()
    {

        $f = new FieldList
        (
            $rootTab = new TabSet("Root", $tabMain = new Tab('Main'))
        );

        $f->addFieldToTab('Root.Main', new TextField('Name','Name'));
        $f->addFieldToTab('Root.Main', new HtmlEditorField('Description','Description'));

        $f->addFieldToTab('Root.Main', new HiddenField('SummitID','SummitID'));
        return $f;
    }

    public function inferLocationType()
    {
        return 'None';
    }

    public function onBeforeWrite()
    {
        parent::onBeforeWrite();
        $this->LocationType = $this->inferLocationType();
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

}