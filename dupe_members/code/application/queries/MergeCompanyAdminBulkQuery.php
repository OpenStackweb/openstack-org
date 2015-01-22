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
 * Class MergeCompanyAdminBulkQuery
 */
final class MergeCompanyAdminBulkQuery extends AbstractMergeBulkQuery {

    /**
     * @return string[]
     */
    public function toSQL()
    {
        $primary_id = $this->primary_id;
        $dupe_id    = $this->dupe_id;

        return array(
            "UPDATE Company SET SubmitterID = {$primary_id} WHERE SubmitterID = {$dupe_id} ;",
            "CREATE TEMPORARY TABLE IF NOT EXISTS COMPANY_ADMIN_INTERSECTION_{$dupe_id}_{$primary_id} AS
SELECT ID FROM Company_Administrators WHERE MemberID = {$dupe_id}
AND CONCAT(CompanyID,'-',GroupID) NOT IN ( SELECT CONCAT(CompanyID,'-',GroupID) FROM Company_Administrators WHERE MemberID = {$primary_id}  );",
            "UPDATE Company_Administrators SET MemberID = {$primary_id} WHERE MemberID = {$dupe_id} AND ID IN ( SELECT ID FROM COMPANY_ADMIN_INTERSECTION_{$dupe_id}_{$primary_id} ) ;",
            "DROP TABLE COMPANY_ADMIN_INTERSECTION_{$dupe_id}_{$primary_id} ;"
        );

    }
}