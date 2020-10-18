<?php

/**
 * Copyright 2015 Open Infrastructure Foundation
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
final class NewsSentEmailMigration extends AbstractDBMigrationTask
{
    protected $title = "News sent email migration";

    protected $description = "Mark as email sent for all approved news articles";

    function doUp()
    {
        global $database;

        $SQL = <<<SQL
        UPDATE News SET EmailSent = 1 WHERE Approved = 1
SQL;
        DB::query($SQL);


    }

    function doDown()
    {

    }
}
