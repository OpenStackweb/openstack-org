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
final class NewsChangeNewsDatesToCentral extends AbstractDBMigrationTask
{
    protected $title = "News Change Dates To Central";

    protected $description = "embargo and expire dates where input as Central but stored as GMT, so we need to +5 hours to all dates for unpublished articles";

    function doUp()
    {
        global $database;

        $SQL = <<<SQL
UPDATE News SET DateEmbargo = IF(DateEmbargo IS NOT NULL, ADDTIME(DateEmbargo, '05:00:00'), DateEmbargo),
DateExpire = IF(DateExpire IS NOT NULL, ADDTIME(DateExpire, '05:00:00'), DateExpire) WHERE Archived = 0 AND Approved = 0;
SQL;

        DB::query($SQL);
    }

    function doDown()
    {

    }
}
