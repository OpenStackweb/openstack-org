<?php

/**
 * Copyright 2017 Open Infrastructure Foundation
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
final class CreateJobsManagerGroup extends AbstractDBMigrationTask
{
    protected $title = "Create Jobs managers groups";

    protected $description = "creates group jobs-managers";

    function doUp()
    {
        global $database;

        $group = new Group();
        $group->setTitle('Jobs Managers');
        $group->setDescription('Sangria Access and Receives notification when a new job request is posted for review.');
        $group->setSlug('jobs-managers');
        $group->write();

        Permission::grant($group->getIdentifier(), 'SANGRIA_ACCESS');

    }

    function doDown()
    {

    }
}
