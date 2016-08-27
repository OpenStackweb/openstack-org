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
final class AddUsersToFoundationStaffMigration extends AbstractDBMigrationTask
{
    protected $title = "Add Users To Foundation Staff Group";

    protected $description = "add a bunch of users to this group so they are displayed in foundation/staff.";

    function doUp()
    {
        global $database;
        $group_repository = new SapphireSecurityGroupRepository();
        $member_repository = new SapphireMemberRepository();

        // Jonathan Bryce, Mark Collier, Lauren Sell, Heidi Bretz, Thierry Carrez, Todd Morey, Wes Wilson, Jimmy McArthur, Jeremy Stanley
        // Clark Boylan, Mike Perez, Heidi Joy Tretheway, Chris Hoge, Scott Raschke, Danny Carreno, Claire Massey, Allison Price
        // Erin Disney, Anne Bertucio, Kendall Waters, David Flanders
        $staff_members = array(28,31,4,7506,154,2138,20997,1395,5479,1092,4840,37953,10331,26518,35643,6997,17777,50757,53381,29123,39506);
        $group = $group_repository->getByCode('openstack-foundation-staff');

        foreach ($staff_members as $order => $member_id) {
            $member = $member_repository->getById($member_id);
            $order++;
            $group->Members()->add($member, array('SortOrder' => $order));
        }

        // Tom Fifield, Kathy Cacciatore, Mark Radcliffe, Lisa Miller
        $sup_members = array(369,7564,1429,7565);
        $group = $group_repository->getByCode('supporting-cast');

        foreach ($sup_members as $order => $member_id) {
            $member = $member_repository->getById($member_id);
            $order++;
            $group->Members()->add($member, array('SortOrder' => $order));
        }

    }

    function doDown()
    {

    }
}
