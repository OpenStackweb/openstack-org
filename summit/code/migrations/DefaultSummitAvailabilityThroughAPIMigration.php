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
class DefaultSummitAvailabilityThroughAPIMigration extends AbstractDBMigrationTask
{
    protected $title = "DefaultSummitAvailabilityThroughAPIMigration";

    protected $description = "DefaultSummitAvailabilityThroughAPIMigration";

    function doUp()
    {

        Summit::$validation_enabled = false;
        // austin
        $summit = Summit::get()->byID(6);
        if(!is_null($summit)){
            $summit->AvailableOnApi = true;
            $summit->write();
        }

        // BCN
        $summit = Summit::get()->byID(7);
        if(!is_null($summit)){
            $summit->AvailableOnApi = true;
            $summit->write();
        }

        Summit::$validation_enabled = true;
    }

    function doDown()
    {

    }
}