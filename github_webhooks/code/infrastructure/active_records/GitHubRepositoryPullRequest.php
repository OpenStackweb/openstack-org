<?php
/**
 * Copyright 2017 OpenStack Foundation
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
 * Class GitHubRepositoryPullRequest
 */
class GitHubRepositoryPullRequest extends DataObject
{
    static $db = [
        'Body'          => 'Text',
        'RejectReason'  => "Enum('None,NotMember,NotFoundationMember,NotCCLATeam','None')",
        'Processed'     => 'Boolean',
        'ProcessedDate' => 'SS_Datetime',
    ];

    static private $has_one = [
        'GitHubRepository' => 'GitHubRepositoryConfiguration'
    ];

    static $defaults = [
         'Processed' => false,
    ];

    /**
     * @var array
     */
    private static $summary_fields = [

        'ID'                      => 'ID',
        'Processed'               => 'Processed',
        'RejectReason'            => 'RejectReason',
    ];

    public function markAsProcessed(){
        $this->ProcessedDate = MySQLDatabase56::nowRfc2822();
        $this->Processed = 1;
    }

}