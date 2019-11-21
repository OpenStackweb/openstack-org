<?php
/**
 * Copyright 2019 Openstack Foundation
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
 * Class Election2018Migration
 */
final class Election2018Migration extends AbstractDBMigrationTask {

    protected $title = "Election2018Migration";

    protected $description = "Election2018Migration";

    function doUp()
    {

        // insert 2019 voters data on 2018 election
        $sql = <<<SQL
insert into ElectionVote (VoterID, ElectionID)
select VoterID,44595 from ElectionVote where ElectionID = 44596;
SQL;

        DB::query($sql);

    }

}