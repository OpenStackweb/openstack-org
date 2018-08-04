<?php
/**
 * Copyright 2018 Openstack Foundation
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

class ReleaseCycleContributor extends DataObject implements IReleaseCycleContributor
{

    private static $create_table_options = array('MySQLDatabase' => 'ENGINE=InnoDB');

    private static $db = array
    (
        'FirstName'     => 'Varchar(255)',
        'LastName'      => 'Varchar(255)',
        'LastCommit'    => 'SS_Datetime',
        'FirstCommit'   => 'SS_Datetime',
        'Email'         => 'Varchar(255)',
        'IRCHandle'     => 'Varchar(100)',
        'CommitCount'   => 'Int',
        'ExtraEmails'   => 'Text'
    );

    private static $has_one = array
    (
        "Member"    => "Member",
        "Release"   => "OpenstackRelease"
    );

    private static $summary_fields = array
    (
        'FirstName'     => 'FirstName',
        'LastName'      => 'LastName',
        'Email'         => 'Email'
    );

    /**
     * @return int
     */
    public function getIdentifier()
    {
        return (int)$this->getField('ID');
    }

    public function toJsonReady() {
        $contributor = [
            'id'              => $this->ID,
            'first_name'      => $this->FirstName,
            'last_name'       => $this->LastName,
            'email'           => $this->Email,
            'commit_count'    => $this->CommitCount,
            'first_commit'    => $this->FirstCommit,
            'last_commit'     => $this->LastCommit,
            'city'            => '',
            'state'           => '',
            'country'         => '',
            'release'         => $this->Release()->Name
        ];

        if ($member = $this->Member()) {
            $contributor['city'] = $member->City;
            $contributor['state'] = $member->State;
            $contributor['country'] = $member->Country;
        }

        return $contributor;
    }

}