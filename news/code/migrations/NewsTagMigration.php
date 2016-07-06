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
final class NewsTagMigration extends AbstractDBMigrationTask
{
    protected $title = "News tag migration";

    protected $description = "new tag class created specific for news, need to migrate the used tags to this new class";

    function doUp()
    {
        global $database;

        $SQL = <<<SQL
        INSERT INTO NewsTag (ID,ClassName,Tag)
        (SELECT NT.TagID AS ID, 'NewsTag' AS ClassName, T.Tag AS Tag FROM News_Tags NT LEFT JOIN Tag T ON T.ID = NT.TagID GROUP BY NT.TagID)
SQL;
        DB::query($SQL);

        $SQL = <<<SQL
        ALTER TABLE News_Tags DROP COLUMN NewsTagID, CHANGE TagID NewsTagID INT NOT NULL
SQL;
        DB::query($SQL);

        $SQL = <<<SQL
        DELETE FROM Tag WHERE NOT EXISTS (SELECT * FROM SummitEvent_Tags WHERE SummitEvent_Tags.TagID = Tag.ID)
SQL;
        DB::query($SQL);


    }

    function doDown()
    {

    }
}
