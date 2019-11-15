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
class ShanghaiPresentationSlugMigration extends AbstractDBMigrationTask
{
    protected $title = "ShanghaiPresentationSlugMigration";

    protected $description = "ShanghaiPresentationSlugMigration";

    function doUp()
    {
        global $database;
        foreach (Presentation::get()->filter("SummitID", 27) as $pres){
            // generate slug
            try {
                $pres->write();

            } catch (Exception $e) {

            }
        }
    }

    function doDown()
    {

    }
}