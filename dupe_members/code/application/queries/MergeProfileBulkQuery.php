<?php
/**
 * Copyright 2014 Openstack Foundation
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
 * Class MergeProfileBulkQuery
 */
final class MergeProfileBulkQuery
    extends AbstractMergeBulkQuery {
    /**
     * @return string[]
     */
    public function toSQL()
    {
        $primary_id = $this->primary_id;
        $dupe_id    = $this->dupe_id;

        return array(
            "UPDATE AffiliationUpdate SET MemberID = {$primary_id} WHERE MemberID = {$dupe_id} ;",
            "UPDATE Affiliation SET MemberID = {$primary_id} WHERE MemberID = {$dupe_id} ;",
            "CREATE TEMPORARY TABLE IF NOT EXISTS GROUPS_INTERSECTION_{$dupe_id}_{$primary_id} AS SELECT ID from Group_Members where MemberID = {$dupe_id} AND GroupID NOT IN ( SELECT GroupID from Group_Members where MemberID = {$primary_id} ) ;",
            "UPDATE Group_Members SET MemberID = {$primary_id} WHERE MemberID = {$dupe_id} AND ID IN ( SELECT ID FROM GROUPS_INTERSECTION_{$dupe_id}_{$primary_id} ) ;",
            "DELETE FROM Group_Members WHERE MemberID =  {$dupe_id} ",
            "DROP TABLE GROUPS_INTERSECTION_{$dupe_id}_{$primary_id} ;",
            "CREATE TEMPORARY TABLE IF NOT EXISTS LEGAL_DOCS_INTERSECTION_{$dupe_id}_{$primary_id} AS SELECT ID from LegalAgreement where MemberID = {$dupe_id} AND  LegalDocumentPageID NOT IN ( SELECT LegalDocumentPageID from LegalAgreement  where MemberID = {$primary_id} ) ;",
            "UPDATE LegalAgreement SET MemberID = {$primary_id} WHERE MemberID = {$dupe_id} AND ID IN ( SELECT ID FROM LEGAL_DOCS_INTERSECTION_{$dupe_id}_{$primary_id} ) ;",
            "DELETE FROM LegalAgreement WHERE MemberID = {$dupe_id};",
            "DROP TABLE LEGAL_DOCS_INTERSECTION_{$dupe_id}_{$primary_id} ;",
            "UPDATE OrganizationRegistrationRequest SET  MemberID = {$primary_id} WHERE MemberID = {$dupe_id} ;",
            "CREATE TEMPORARY TABLE IF NOT EXISTS TEAM_MEMBERS_INTERSECTION_{$dupe_id}_{$primary_id} AS
SELECT ID from Team_Members where MemberID = {$dupe_id} AND
TeamID NOT IN ( SELECT TeamID from Team_Members  where MemberID = {$primary_id} ) ;",
            "UPDATE Team_Members SET MemberID = {$primary_id} WHERE MemberID = {$dupe_id} AND ID IN ( SELECT ID FROM TEAM_MEMBERS_INTERSECTION_{$dupe_id}_{$primary_id} ) ;",
            "DELETE FROM Team_Members WHERE MemberID = {$dupe_id} ;",
            "DROP TABLE TEAM_MEMBERS_INTERSECTION_{$dupe_id}_{$primary_id} ;",
            "UPDATE TeamInvitation SET  MemberID = {$primary_id} WHERE MemberID = {$dupe_id} ;",
            "UPDATE OrganizationRegistrationRequest SET  MemberID = {$primary_id} WHERE MemberID = {$dupe_id} ;",
            "UPDATE JobRegistrationRequest SET MemberID = {$primary_id} WHERE MemberID = {$dupe_id} ;",
            "UPDATE EventRegistrationRequest SET MemberID = {$primary_id} WHERE MemberID = {$dupe_id} ;"
        );

    }
}