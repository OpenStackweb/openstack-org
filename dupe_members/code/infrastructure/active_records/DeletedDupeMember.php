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
 * Class DeletedDupeMember
 */
final class DeletedDupeMember
    extends DataObject
    implements IDeletedDupeMember
{

    static $create_table_options = array('MySQLDatabase' => 'ENGINE=InnoDB');

    private static $db = array(
        'MemberID' => 'Int',
        'FirstName' => 'Varchar',
        'Surname' => 'Varchar',
        'Email' => 'Varchar(256)', // See RFC 5321, Section 4.5.3.1.3.
        'Password' => 'Varchar(160)',
        // This is an arbitrary code pointing to a PasswordEncryptor instance,
        // not an actual encryption algorithm.
        // Warning: Never change this field after its the first password hashing without
        // providing a new cleartext password as well.
        'PasswordEncryption' => "Varchar(50)",
        'Salt' => 'Varchar(50)',
        'PasswordExpiry' => 'Date',
        'LockedOutUntil' => 'SS_Datetime',
        'Locale' => 'Varchar(6)',
        // In ISO format
        'DateFormat' => 'Varchar(30)',
        'TimeFormat' => 'Varchar(30)',
        'SecondEmail' => 'Text',
        'ThirdEmail' => 'Text',
        'HasBeenEmailed' => 'Boolean',
        'ShirtSize' => "Enum('Extra Small, Small, Medium, Large, XL, XXL')",
        'StatementOfInterest' => 'Text',
        'Bio' => 'HTMLText',
        'FoodPreference' => 'Text',
        'OtherFood' => 'Text',
        'IRCHandle' => 'Text',
        'TwitterName' => 'Text',
        'Projects' => 'Text',
        'OtherProject' => 'Text',
        'SubscribedToNewsletter' => 'Boolean',
        'JobTitle'=>'Text',
        'DisplayOnSite'=>'Boolean',
        'Role'=>'Text',
        'LinkedInProfile'=> 'Text',
        'Address'=>'Varchar(255)',
        'Suburb'=>'Varchar(64)',
        'State'=>'Varchar(64)',
        'Postcode'=>'Varchar(64)',
        'Country'=>'Varchar(2)',
        'City'=>'Varchar(64)',
        'Gender'=>'Varchar(32)',
        'TypeOfDirector' => 'Text',
        'CLASigned' => 'Boolean',
        'LastCodeCommit' => 'SS_Datetime',
        'GerritID' => 'Text'
    );

    /**
     * @return int
     */
    public function getIdentifier()
    {
        return (int)$this->getField('ID');
    }
}