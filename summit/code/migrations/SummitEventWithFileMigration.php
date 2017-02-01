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
final class SummitEventWithFileMigration extends AbstractDBMigrationTask
{
    protected $title = "SummitEventWithFileMigration";

    protected $description = "migrate lunch and breaks event types";

    function doUp()
    {
        global $database;

        $boston_summit = Summit::get()->byID(22);
        Summit::seedBasicEventTypes($boston_summit->ID);
        $old_type = $boston_summit->EventTypes()->filter('Type',ISummitEventType::Lunch_Breaks)->first();

        $old_type->delete();

    }

    function doDown()
    {
        // TODO: Implement doDown() method.
    }
}