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

final class AddUniqueIndexMemberScheduleFavorites extends AbstractDBMigrationTask
{
    protected $title = "AddUniqueIndexMemberScheduleFavorites";

    protected $description = "AddUniqueIndexMemberScheduleFavorites";

    function doUp()
    {
        global $database;
        if(!DBSchema::existsIndex($database, 'Member_FavoriteSummitEvents', 'IDX_MEMBER_SUMMIT_EVENT')){
            DB::query("CREATE UNIQUE INDEX IDX_MEMBER_SUMMIT_EVENT
    ON Member_FavoriteSummitEvents (MemberID, SummitEventID);");
        }

        if(!DBSchema::existsIndex($database, 'Member_Schedule', 'IDX_MEMBER_SUMMIT_EVENT')){
            DB::query("CREATE UNIQUE INDEX IDX_MEMBER_SUMMIT_EVENT
    ON Member_Schedule (MemberID, SummitEventID);");
        }
    }

    function doDown()
    {

    }
}