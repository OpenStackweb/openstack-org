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
 * Class GerritChangeInfo
 * https://gerrit-review.googlesource.com/Documentation/rest-api-changes.html#change-info
 */
class GerritChangeInfo extends DataObject {

    private static $db = array(
        "kind" => 'Text',
        "FormattedChangeId" => 'Text',
        "ProjectName" => 'Text',
        "Branch" => 'Text',
        "Topic" => 'Text',
        "ChangeId" => 'Varchar(128)',
        "Subject" => 'Text',
        "Status" => 'Text',
        "CreatedDate" => 'SS_Datetime',
        "UpdatedDate" => 'SS_Datetime',
    );

    static $indexes = array(
        'ChangeId' => array('type'=>'unique', 'value'=>'ChangeId')
    );

    static private $has_one = array(
        'Member' => 'Member'
    );

}