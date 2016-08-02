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
final class MarketingPageTableCompressionMigration extends AbstractDBMigrationTask
{
    protected $title = "MarketingPage Table Compression Migration";

    protected $description = "change table for this page to allow large fields";

    function doUp()
    {
        global $database;

        $SQL = <<<SQL
    ALTER TABLE MarketingPage
    ENGINE=InnoDB
    ROW_FORMAT=COMPRESSED
    KEY_BLOCK_SIZE=8
SQL;
        DB::query($SQL);

        $SQL = <<<SQL
    ALTER TABLE MarketingPage_live
    ENGINE=InnoDB
    ROW_FORMAT=COMPRESSED
    KEY_BLOCK_SIZE=8
SQL;
        DB::query($SQL);

        $SQL = <<<SQL
    ALTER TABLE MarketingPage_versions
    ENGINE=InnoDB
    ROW_FORMAT=COMPRESSED
    KEY_BLOCK_SIZE=8
SQL;
        DB::query($SQL);


    }

    function doDown()
    {

    }
}
