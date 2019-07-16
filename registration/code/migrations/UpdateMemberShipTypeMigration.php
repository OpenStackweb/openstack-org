<?php
/**
 * Copyright 2020 OpenStack Foundation
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

/**
 * Class UpdateMemberShipTypeMigration
 */
final class UpdateMemberShipTypeMigration extends AbstractDBMigrationTask
{

    protected $title = "UpdateMemberShipTypeMigration";

    protected $description = "UpdateMemberShipTypeMigration";

    function doUp()
    {
        global $database;

        DB::query("UPDATE Member set MembershipType = 'Foundation'
WHERE EXISTS (SELECT Group_Members.MemberID FROM Group_Members  
                    INNER JOIN `Group` ON `Group`.ID = Group_Members.GroupID
             WHERE `Group`.Code = 'foundation-members' AND Group_Members.MemberID = Member.ID)");


        DB::query("
        UPDATE Member set MembershipType = 'Community'
WHERE EXISTS (SELECT Group_Members.MemberID FROM Group_Members
                    INNER JOIN `Group` ON `Group`.ID = Group_Members.GroupID
             WHERE `Group`.Code = 'community-members' AND Group_Members.MemberID = Member.ID)
        ");

    }

    function doDown()
    {
        // TODO: Implement doDown() method.
    }
}