<?php
/**
 * Copyright 2018 Open Infrastructure Foundation
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
 * Class AUCMetricMissMatchError
 */
final class AUCMetricMissMatchError extends DataObject
{
    /**
     * @var array
     */
    private static $db = [
        'ServiceIdentifier' => 'Varchar',
        'UserIdentifier'    => 'Text',
        'Solved'            => 'Boolean',
        'SolvedDate'        => 'SS_Datetime',
    ];

    /**
     * @var array
     */
    private static $has_one = [
        'SolvedBy' => 'Member'
    ];

    /**
     * @return $this
     */
    public function markAsSolved(){
        $this->Solved     = true;
        $this->SolvedDate = SS_Datetime::now()->Rfc2822();
        $this->SolvedByID = Member::currentUserID();
        return $this;
    }
}