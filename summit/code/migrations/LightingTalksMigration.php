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
final class LightingTalksMigration extends AbstractDBMigrationTask
{
    protected $title = "LightingTalksMigration";

    protected $description = "LightingTalksMigration";

    function doUp()
    {
        global $database;

        $summit_list = [6, 7, 22];

        foreach($summit_list as $summit_id) {

            Summit::seedBasicEventTypes($summit_id);
            $lightning_talk_type = PresentationType::get()->filter(['SummitID' => $summit_id, 'Type' => IPresentationType::LightingTalks])->first();
            $lightning_talk_presentations = Presentation::get()->filter(["SummitID" => $summit_id, 'LightningTalk' => true]);

            foreach ($lightning_talk_presentations as $p) {
                $p->TypeID = $lightning_talk_type->ID;
                $p->write();
            }
        }

        if(DBSchema::existsColumn($database, "Presentation", "LightningTalk")){
            DBSchema::dropColumn($database, "Presentation", "LightningTalk");
        }

    }

    function doDown()
    {
        // TODO: Implement doDown() method.
    }
}