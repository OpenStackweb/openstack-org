<?php
/**
 * Copyright 2016 OpenStack Foundation
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

class NormalizeTagsMigration extends AbstractDBMigrationTask
{
    protected $title = "NormalizeTagsMigration";

    protected $description = "NormalizeTagsMigration";

    function doUp()
    {
        global $database;

        $rows_duplicates = DB::query("SELECT UPPER(TRIM(Tag)) AS Tag, COUNT(*) FROM Tag GROUP BY UPPER(TRIM(Tag)) HAVING COUNT(*) > 1");

        foreach ($rows_duplicates as $row_tag_dup) {
            $tag = $row_tag_dup['Tag'];
            if(empty(trim($tag))) continue;
            // get dup tags

            $tags     = DB::query("SELECT * from Tag WHERE UPPER(TRIM(Tag)) = UPPER(TRIM('{$tag}')) ;");
            $root_tag = $tags->first();
            $root_id  = $root_tag['ID'];

            foreach ($tags as $tag) {

                $tag_id = intval($tag['ID']);

                if ($tag_id == $root_id ) continue;

                DB::query("UPDATE SummitEvent_Tags SET TagID =  {$root_id} WHERE TagID = {$tag_id};");
                DB::query("DELETE Tag FROM Tag WHERE ID = {$tag_id}; ");
            }
        }

        DB::query("UPDATE Tag SET Tag = TRIM(Tag);");
    }

    function doDown()
    {
        // TODO: Implement doDown() method.
    }
}