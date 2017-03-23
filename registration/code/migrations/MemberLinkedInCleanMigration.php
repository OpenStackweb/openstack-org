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
final class MemberLinkedInCleanMigration extends AbstractDBMigrationTask
{
    protected $title = "MemberLinkedInCleanMigration";

    protected $description = "Clean linkedin user to use full url";

    function doUp()
    {
        global $database;

        $members = Member::get()->where("LinkedInProfile IS NOT NULL AND LinkedInProfile NOT LIKE 'http%'");
        foreach ($members as $member) {
            $linkedin = $member->LinkedInProfile;
            if (strpos($linkedin,'linkedin') === false) {
                $member->LinkedInProfile = 'https://www.linkedin.com/'.$linkedin;
            } else {
                $member->LinkedInProfile = 'https://'.$linkedin;
            }

            $member->write();
        }
    }

    function doDown()
    {
        // TODO: Implement doDown() method.
    }
}