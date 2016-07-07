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
final class FixSummitEventTypesMigration extends AbstractDBMigrationTask
{
    protected $title = "Fix Summit Event Types Migration";

    protected $description = "For a while there were two Panel Types to choose for a new presentation in call-for-presentations,
                              this migration is to fix any wrong assigned events";

    function doUp()
    {
        global $database;

        $result = DB::query("SELECT E.ID,E.SummitID,E.TypeID,ET.Type FROM SummitEvent AS E LEFT JOIN SummitEventType AS ET ON ET.ID = E.TypeID WHERE ET.SummitID != E.SummitID");

        foreach ($result as $event) {
            $summit_id = $event['SummitID'];
            $event_id = $event['ID'];
            $old_type = $event['Type'];
            $new_type_id = SummitEventType::get()->where("Type = '$old_type' AND SummitID = $summit_id")->first()->ID;
            DB::query("UPDATE SummitEvent SET TypeID = $new_type_id WHERE ID = $event_id");
        }

    }

    function doDown()
    {
        // TODO: Implement doDown() method.
    }
}