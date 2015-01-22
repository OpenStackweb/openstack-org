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
 * Interface IMergeAccountBulkQueryFactory
 */
interface IMergeAccountBulkQueryFactory {


    /**
     * @param ICommunityMember $primary_account Primary Account
     * @param ICommunityMember $dupe_account Account to merge with
     * @return IBulkQuery
     */
    public function buildMergeCandidateBulkQuery(ICommunityMember $primary_account, ICommunityMember $dupe_account);

    /**
     * @param ICommunityMember $primary_account Primary Account
     * @param ICommunityMember $dupe_account Account to merge with
     * @return IBulkQuery
     */
    public function buildMergeSpeakerBulkQuery(ICommunityMember $primary_account, ICommunityMember $dupe_account);

    /**
     * @param ICommunityMember $primary_account Primary Account
     * @param ICommunityMember $dupe_account Account to merge with
     * @return IBulkQuery
     */
    public function buildMergeMarketPlaceAdminBulkQuery(ICommunityMember $primary_account, ICommunityMember $dupe_account);

    /**
     * @param ICommunityMember $primary_account Primary Account
     * @param ICommunityMember $dupe_account Account to merge with
     * @return IBulkQuery
     */
    public function buildMergeCompanyAdminBulkQuery(ICommunityMember $primary_account, ICommunityMember $dupe_account);

     /**
     * @param ICommunityMember $primary_account Primary Account
     * @param ICommunityMember $dupe_account Account to merge with
     * @return IBulkQuery
     */
    public function buildMergeDeploymentSurveysBulkQuery(ICommunityMember $primary_account, ICommunityMember $dupe_account);

    /**
     * @param ICommunityMember $primary_account Primary Account
     * @param ICommunityMember $dupe_account Account to merge with
     * @return IBulkQuery
     */
    public function buildMergeAppDevSurveysBulkQuery(ICommunityMember $primary_account, ICommunityMember $dupe_account);

    /**
     * @param ICommunityMember $primary_account Primary Account
     * @param ICommunityMember $dupe_account Account to merge with
     * @return IBulkQuery
     */
    public function buildMergeProfileBulkQuery(ICommunityMember $primary_account, ICommunityMember $dupe_account);

} 