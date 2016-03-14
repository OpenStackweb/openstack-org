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
final class ChangePremiseForPremisesMigration extends AbstractDBMigrationTask
{
    protected $title = "Change Premise For Premises on User Stories";

    protected $description = "changes deployment type word 'premise' for 'premises' on all user stories";

    function doUp()
    {
        global $database;

        $SQL = <<<SQL
UPDATE UserStory set DeploymentType = REPLACE(DeploymentType,'On-Premise','On-Premises') WHERE DeploymentType LIKE 'On-Premise %';
SQL;

        DB::query($SQL);

        $SQL = <<<SQL
UPDATE Deployment set DeploymentType = REPLACE(DeploymentType,'On-Premise','On-Premises') WHERE DeploymentType LIKE 'On-Premise %';
SQL;

        DB::query($SQL);
    }

    function doDown()
    {

    }
}
