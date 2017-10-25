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
final class MemberLinkedInUrlMigration extends AbstractDBMigrationTask
{
    protected $title = "MemberLinkedInUrlMigration";

    protected $description = "Change Url linkedin field";

    function doUp()
    {
        global $database;

        $members = Member::get()->where("
            LinkedInProfile IS NOT NULL AND LinkedInProfile NOT LIKE '%/in/%'
            AND LinkedInProfile NOT LIKE '%/pub/%' AND LinkedInProfile NOT LIKE '%/profile/%'
            AND LinkedInProfile NOT LIKE '%/company/%' AND LinkedInProfile LIKE '%www.linkedin.com%'"
        );

        foreach ($members as $member) {
            $linkedin = str_replace(' ','',$member->LinkedInProfile);
            $member->LinkedInProfile = substr_replace($linkedin, '/in', 24, 0);

            $member->write();
        }
    }

    function doDown()
    {
        // TODO: Implement doDown() method.
    }
}