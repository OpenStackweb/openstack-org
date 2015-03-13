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
 * Class MergeAccountBulkQueryFactory
 */
final class MergeAccountBulkQueryFactory
    implements IMergeAccountBulkQueryFactory
{

    /**
     * @param ICommunityMember $primary_account Primary Account
     * @param ICommunityMember $dupe_account Account to merge with
     * @return IBulkQuery
     */
    public function buildMergeCandidateBulkQuery(ICommunityMember $primary_account, ICommunityMember $dupe_account)
    {
        $query = new MergeCandidateBulkQuery();

        $query->addParams(array(
            'primary_id' => $primary_account->getIdentifier(),
            'dupe_id' => $dupe_account->getIdentifier()
        ));

        return $query;
    }

    /**
     * @param ICommunityMember $primary_account Primary Account
     * @param ICommunityMember $dupe_account Account to merge with
     * @return IBulkQuery
     */
    public function buildMergeSpeakerBulkQuery(ICommunityMember $primary_account, ICommunityMember $dupe_account)
    {
        $query = new MergeSpeakerBulkQuery();

        $query->addParams(array(
            'primary_id' => $primary_account->getIdentifier(),
            'dupe_id' => $dupe_account->getIdentifier()
        ));

        return $query;
    }

    /**
     * @param ICommunityMember $primary_account Primary Account
     * @param ICommunityMember $dupe_account Account to merge with
     * @return IBulkQuery
     */
    public function buildMergeMarketPlaceAdminBulkQuery(ICommunityMember $primary_account, ICommunityMember $dupe_account)
    {

        $query = new MergeMarketPlaceAdminBulkQuery();

        $query->addParams(array(
            'primary_id' => $primary_account->getIdentifier(),
            'dupe_id' => $dupe_account->getIdentifier()
        ));

        return $query;
    }

    /**
     * @param ICommunityMember $primary_account Primary Account
     * @param ICommunityMember $dupe_account Account to merge with
     * @return IBulkQuery
     */
    public function buildMergeCompanyAdminBulkQuery(ICommunityMember $primary_account, ICommunityMember $dupe_account)
    {

        $query = new MergeCompanyAdminBulkQuery();

        $query->addParams(array(
            'primary_id' => $primary_account->getIdentifier(),
            'dupe_id' => $dupe_account->getIdentifier()
        ));

        return $query;
    }

    /**
     * @param ICommunityMember $primary_account Primary Account
     * @param ICommunityMember $dupe_account Account to merge with
     * @return IBulkQuery
     */
    public function buildMergeDeploymentSurveysBulkQuery(ICommunityMember $primary_account, ICommunityMember $dupe_account)
    {
        $query = new MergeDeploymentSurveysBulkQuery();

        $query->addParams(array(
            'primary_id' => $primary_account->getIdentifier(),
            'dupe_id' => $dupe_account->getIdentifier()
        ));

        return $query;
    }

    /**
     * @param ICommunityMember $primary_account Primary Account
     * @param ICommunityMember $dupe_account Account to merge with
     * @return IBulkQuery
     */
    public function buildMergeAppDevSurveysBulkQuery(ICommunityMember $primary_account, ICommunityMember $dupe_account)
    {
        $query = new MergeAppDevSurveysBulkQuery;

        $query->addParams(array(
            'primary_id' => $primary_account->getIdentifier(),
            'dupe_id' => $dupe_account->getIdentifier()
        ));

        return $query;
    }

    /**
     * @param ICommunityMember $primary_account Primary Account
     * @param ICommunityMember $dupe_account Account to merge with
     * @return IBulkQuery
     */
    public function buildMergeProfileBulkQuery(ICommunityMember $primary_account, ICommunityMember $dupe_account)
    {

        $query = new MergeProfileBulkQuery();

        $query->addParams(array(
            'primary_id' => $primary_account->getIdentifier(),
            'dupe_id' => $dupe_account->getIdentifier()
        ));

        return $query;
    }

    /**
     * @param ICommunityMember $primary_account
     * @param  string          $newEmail
     * @return IBulkQuery
     */
    public function buildMergeEmail(ICommunityMember $primary_account, $newEmail)
    {
        $query = new MergeEmailBulkQuery();
        $query->addParams(array(
            'primary_id' => $primary_account->getIdentifier(),
            'email' => $newEmail
        ));
        return $query;
    }
}