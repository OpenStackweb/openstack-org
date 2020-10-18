<?php

/**
 * Copyright 2017 Open Infrastructure Foundation
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
class OpenStackPoweredProgramHistory extends DataObject
{

    // OpenStack Powered Program attributes
    static $db = [
        'CompatibleWithComputeBefore'  => 'Boolean',
        'CompatibleWithStorageBefore'  => 'Boolean',
        'ExpiryDateBefore'             => 'SS_Datetime',
        'ProgramVersionIDBefore'       => 'Int',
        'ProgramVersionNameBefore'     => 'Varchar',
        'CompatibleWithComputeCurrent' => 'Boolean',
        'CompatibleWithStorageCurrent' => 'Boolean',
        'ExpiryDateCurrent'            => 'SS_Datetime',
        'ProgramVersionIDCurrent'      => 'Int',
        'ProgramVersionNameCurrent'    => 'Varchar',
        'ReportedReleaseIDBefore'      => 'Int',
        'ReportedReleaseIDCurrent'     => 'Int',
        'ReportedReleaseNameBefore'    => 'Varchar',
        'ReportedReleaseNameCurrent'   => 'Varchar',
        'PassedReleaseIDBefore'        => 'Int',
        'PassedReleaseIDCurrent'       => 'Int',
        'PassedReleaseNameBefore'      => 'Varchar',
        'PassedReleaseNameCurrent'     => 'Varchar',
        'NotesBefore'                  => 'Text',
        'NotesCurrent'                 => 'Text',
    ];

    // OpenStack Powered Program attributes
    static $has_one   = [
        'OpenStackImplementation' => 'OpenStackImplementation',
        'Owner'                   => 'Member',
    ];
}