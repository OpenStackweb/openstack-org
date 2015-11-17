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
class SchedRefactoringMigration2 extends AbstractDBMigrationTask
{
    protected $title = "Sched Refactoring #2";

    protected $description = "Sched Refactoring #2";

    function doUp()
    {

        DB::query("INSERT INTO SummitEvent_Tags (SummitEventID, TagID) select PresentationID, TagID from Presentation_Tags;");
        DB::query("DROP TABLE Presentation_Tags;");
        DB::query("UPDATE SummitEvent INNER JOIN Presentation ON Presentation.ID = SummitEvent.ID SET SummitEvent.ShortDescription = Presentation.ShortDescription;");
        DB::query("ALTER TABLE Presentation DROP COLUMN ShortDescription;");
    }

    function doDown()
    {
        // TODO: Implement doDown() method.
    }
}