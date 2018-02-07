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

final class PresentationTagBugMigration extends AbstractDBMigrationTask
{
    protected $title = "PresentationTagBugMigration";

    protected $description = "Presentations have the wrong tags assigned";

    function doUp()
    {
        global $database;

        $wrong_tags = DB::query(" 
            SELECT et.ID AS RelID, t3.ID AS CorrectTagID
            FROM SummitEvent_Tags et
            LEFT JOIN Tag t ON et.TagID = t.ID
            LEFT JOIN SummitEvent e ON e.ID = et.SummitEventID
            LEFT JOIN Tag t3 ON t3.Tag = t.Tag
            LEFT JOIN PresentationCategory_AllowedTags alt ON alt.PresentationCategoryID = e.CategoryID
            WHERE alt.TagID = t3.ID AND e.SummitID = 24 AND t.ID != t3.ID
        ");

        foreach ($wrong_tags as $wt) {
            DB::query("UPDATE SummitEvent_Tags SET TagID = ".$wt['CorrectTagID']." WHERE ID = ".$wt['RelID']);
        }

    }

    function doDown()
    {
        // TODO: Implement doDown() method.
    }
}