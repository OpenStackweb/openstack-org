<?php

/**
 * Copyright 2017 OpenStack Foundation
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
final class CreateJobPostersGroupMigration extends AbstractDBMigrationTask
{
    protected $title = "Create Job Posters Group";

    protected $description = "creates 2 groups one for auto accept job request and one for auto reject";

    function doUp()
    {
        global $database;

        $group = new Group();
        $group->setTitle('Auto Accept Job Post');
        $group->setDescription('Auto accepts job post from users on this group.');
        $group->setSlug('auto-accept-job-post');
        $group->write();

        Permission::grant($group->getIdentifier(), IJobRegistrationRequestManager::AutoAcceptJobPostPermissionSlug);

        $group = new Group();
        $group->setTitle('Auto Reject Job Post');
        $group->setDescription('Auto rejects job post from users on this group.');
        $group->setSlug('auto-reject-job-post');
        $group->write();

        Permission::grant($group->getIdentifier(), IJobRegistrationRequestManager::AutoRejectJobPostPermissionSlug);
    }

    function doDown()
    {

    }
}
