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
final class RemoveGroupMembersSortOrderCol extends AbstractDBMigrationTask
{
    protected $title = "RemoveGroupMembersSortOrderCol";

    protected $description = "remove unused column in relational table";

    function doUp()
    {
        global $database;

        if (DBSchema::existsColumn($database, 'Group_Members', 'SortOrder')) {
            DBSchema::dropColumn($database, 'Group_Members', 'SortOrder');
        }

    }

    function doDown()
    {
        // TODO: Implement doDown() method.
    }
}