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
 * Class SpeakerContactEmail
 */
final class SpeakerContactEmail
    extends DataObject
    implements ISpeakerContactEmail
{

    static $create_table_options = array('MySQLDatabase' => 'ENGINE=InnoDB');

    static $db = array(
        'OrgName' => 'Varchar(255)',
        'OrgEmail' => 'Varchar(255)',
        'EventName' => 'Varchar(255)',
        'Format' => 'Varchar(255)',
        'Attendance' => 'Int',
        'DateOfEvent' => 'Varchar(255)',
        'Location' => 'Varchar(255)',
        'Topics' => 'Varchar(255)',
        'GeneralRequest' => 'Text',
        'EmailSent' => 'Boolean'
    );

    static $has_one = array(
        'Recipient' => 'Speaker',
    );

    /**
     * @return int
     */
    public function getIdentifier()
    {
        return (int)$this->getField('ID');
    }


}