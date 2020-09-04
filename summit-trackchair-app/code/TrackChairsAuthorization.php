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

final class TrackChairsAuthorization
{
    public static function authorize(int $summit_id):bool {
        $member = Member::currentUser();
        if(is_null($member)) return false;
        if($member->isAdmin()) return true;

        if($summit_id > 0) {
            $sql = <<<SQL
SELECT COUNT(DISTINCT(SummitAdministratorPermissionGroup_Summits.SummitID)) 
FROM SummitAdministratorPermissionGroup_Members 
INNER JOIN SummitAdministratorPermissionGroup_Summits ON 
SummitAdministratorPermissionGroup_Summits.SummitAdministratorPermissionGroupID = SummitAdministratorPermissionGroup_Members.SummitAdministratorPermissionGroupID
WHERE SummitAdministratorPermissionGroup_Members.MemberID = {$member->ID}
AND 
SummitAdministratorPermissionGroup_Summits.SummitID = {$summit_id}
SQL;
            $hasPermissionOnSummit = intval(DB::query($sql)->value()) > 0;
            if($member->inGroup('track-chairs-admins') && $hasPermissionOnSummit) return true;
            if($member->inGroup('track-chairs') && $hasPermissionOnSummit) return true;
            return false;
        }


        if($member->inGroup('track-chairs-admins')) return true;
        if($member->inGroup('track-chairs')) return true;
        return false;
    }

    public static function isAdmin(int $summit_id):bool{
        if($summit_id == 0) return false;
        $member = Member::currentUser();
        if(is_null($member)) return false;
        if($member->isAdmin()) return true;
        $sql = <<<SQL
SELECT COUNT(DISTINCT(SummitAdministratorPermissionGroup_Summits.SummitID)) 
FROM SummitAdministratorPermissionGroup_Members 
INNER JOIN SummitAdministratorPermissionGroup_Summits ON 
SummitAdministratorPermissionGroup_Summits.SummitAdministratorPermissionGroupID = SummitAdministratorPermissionGroup_Members.SummitAdministratorPermissionGroupID
WHERE SummitAdministratorPermissionGroup_Members.MemberID = {$member->ID}
AND 
SummitAdministratorPermissionGroup_Summits.SummitID = {$summit_id}
SQL;
        $hasPermissionOnSummit = intval(DB::query($sql)->value()) > 0;

        if($member->inGroup('track-chairs-admins') && $hasPermissionOnSummit) return true;
        return false;
    }
}