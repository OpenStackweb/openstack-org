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
final class MemberSpammerMigration extends AbstractDBMigrationTask{

    protected $title = "MemberSpammerMigration";

    protected $description = "MemberSpammerMigration";

    function doUp()
    {
        global $database;

        DB::query("UPDATE Member SET Type = 'Spam', Active = 0
WHERE ID IN (31350, 14236, 33524);");

        DB::query("UPDATE Member SET Type = 'Ham'
WHERE ID NOT IN (31350, 14236, 33524);");

        DB::query("UPDATE Member SET Type = 'Spam', Active = 0
WHERE Email LIKE '%66cloud.xyz%'");

        DB::query("UPDATE Member SET Type = 'Spam', Active = 0
WHERE Email LIKE '%kystack.xyz%'");

        DB::query("TRUNCATE TABLE MemberEstimatorFeed;");

        DB::query("INSERT INTO MemberEstimatorFeed
(FirstName, Surname, Email, Type)
SELECT FirstName, Surname, Email, Type FROM Member 
WHERE Member.Type = 'Spam'");

        db::query("INSERT INTO MemberEstimatorFeed
(FirstName, Surname, Email, Type)
SELECT FirstName, Surname, Email, Type FROM Member 
WHERE Member.Type = 'Ham' LIMIT 0, 300;");

    }

    function doDown()
    {
        // TODO: Implement doDown() method.
    }
}