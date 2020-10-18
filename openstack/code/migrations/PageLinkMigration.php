<?php
/**
 * Copyright 2018 Open Infrastructure Foundation
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

final class PageLinkMigration extends AbstractDBMigrationTask
{
    protected $title = "PageLinkMigration";

    protected $description = "PageLinkMigration";

    function doUp()
    {

        $sql = <<<SQL
INSERT INTO OpenStackComponentLink
(ID, ComponentID) SELECT ID, PageID FROM Link WHERE Created >= '2018-09-11 00:00:00';
UPDATE Link SET ClassName = 'OpenStackComponentLink' WHERE Created >= '2018-09-11 00:00:00';
INSERT INTO PageLink
(ID, PageID) SELECT ID, PageID FROM Link WHERE ClassName = 'Link';
UPDATE Link set ClassName = 'PageLink' WHERE ClassName = 'Link';
ALTER TABLE Link DROP COLUMN PageID;
SQL;

        foreach(explode(';', $sql) as $statement)
            if(!empty($statement)) DB::query($statement);
    }

    function doDown()
    {
        // TODO: Implement doDown() method.
    }
}