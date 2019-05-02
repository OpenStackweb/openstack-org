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
     * @param Member $primary_account Primary Account
     * @param Member $dupe_account Account to merge with
     * @return IBulkQuery
     */
    public function buildMergeCandidateBulkQuery(Member $primary_account, Member $dupe_account);

    /**
     * @param Member $primary_account Primary Account
     * @param Member $dupe_account Account to merge with
     * @return IBulkQuery
     */
    public function buildMergeSpeakerBulkQuery(Member $primary_account, Member $dupe_account);

    /**
     * @param Member $primary_account Primary Account
     * @param Member $dupe_account Account to merge with
     * @return IBulkQuery
     */
    public function buildMergeMarketPlaceAdminBulkQuery(Member $primary_account, Member $dupe_account);

    /**
     * @param Member $primary_account Primary Account
     * @param Member $dupe_account Account to merge with
     * @return IBulkQuery
     */
    public function buildMergeCompanyAdminBulkQuery(Member $primary_account, Member $dupe_account);

     /**
     * @param Member $primary_account Primary Account
     * @param Member $dupe_account Account to merge with
     * @return IBulkQuery
     */
    public function buildMergeSurveysBulkQuery(Member $primary_account, Member $dupe_account);

    /**
     * @param Member $primary_account Primary Account
     * @param Member $dupe_account Account to merge with
     * @return IBulkQuery
     */
    public function buildMergeAttendeeBulkQuery(Member $primary_account, Member $dupe_account);

    /**
     * @param Member $primary_account Primary Account
     * @param Member $dupe_account Account to merge with
     * @return IBulkQuery
     */
    public function buildMergeProfileBulkQuery(Member $primary_account, Member $dupe_account);

    /**
     * @param Member $primary_account
     * @param  string      $newEmail
     * @return IBulkQuery
     */
    public function buildMergeEmail(Member $primary_account, $newEmail);

} 