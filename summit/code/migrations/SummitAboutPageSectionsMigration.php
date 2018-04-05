<?php

/**
 * Copyright 2018 OpenStack Foundation
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
final class SummitAboutPageSectionsMigration extends AbstractDBMigrationTask
{
    protected $title = "SummitAboutPageSectionsMigration";

    protected $description = "SummitAboutPageSectionsMigration";

    function doUp()
    {
        global $database;

        $res = DB::query("SELECT * FROM SummitAboutPage_PageSections;");

        foreach($res as $row) {
            $page_section = PageSection::get()->byID($row['PageSectionID']);

            if(!is_null($page_section)) {
                $page_section->Order = $row['Order'];
                $page_section->ParentPageID = $row['SummitAboutPageID'];
                $page_section->write();
            }
        }

        DB::query("DROP TABLE SummitAboutPage_PageSections;");

    }

    function doDown()
    {
        // TODO: Implement doDown() method.
    }
}