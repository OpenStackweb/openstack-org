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

/**
 * Class SummitTicketType
 * https://www.eventbrite.com/developer/v3/endpoints/events/#ebapi-get-events-id-ticket-classes-ticket-class-id
 */
class SummitTicketType extends DataObject implements ISummitTicketType
{
    private static $db = array
    (
        'ExternalId'  => 'Varchar(255)',
        'Name'        => 'Text',
        'Description' => 'Text',
    );

    private static $has_one = array
    (
        'Summit' => 'Summit'
    );

    private static $has_many = array
    (

    );

    static $indexes = array
    (
       'Summit_ExternalId' => array('type' => 'unique', 'value' => 'SummitID,ExternalId')
    );

    private static $summary_fields = array
    (
        'Name'       => 'Name',
        'ExternalId' => 'ExternalId',
    );

    private static $searchable_fields = array
    (
    );



    // CMS admin UI
    public function getCMSFields()
    {
        $f = new FieldList
        (
            array
            (
                new HiddenField('SummitID', 'SummitID'),
                new TextField('Name', 'Name'),
                new TextField('ExternalId', 'Eventbrite External Id'),
            )
        );

        return $f;
    }

    protected function validate()
    {
        return parent::validate();
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
    public function getExternalId()
    {
       return $this->getField('ExternalId');
    }

    /**
     * @return string
     */
    public function getName()
    {
       return $this->getField('Name');
    }

    /**
     * @return ISummit
     */
    public function getSummit()
    {
       return AssociationFactory::getInstance()->getMany2OneAssociation($this, 'Summit')->getTarget();
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