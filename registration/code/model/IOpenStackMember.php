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

interface IOpenStackMember
{
    /**
     * @return string
     */
    public function getFullName();

    /**
     * @param int $width
     * @return string
     */
    public function ProfilePhoto($width = 100);

    /*
     * Get all managed companies on where user is an admin (old way CompanyAdminID and new way through security groups)
     */
    public function getManagedCompanies();

    /**
     * @return Affiliation[]
     */
    public function OrderedAffiliations();

    /**
     * @return Affiliation
     */
    public function getCurrentAffiliation();

    /**
     * @return Org
     */
    public function getCurrentOrganization();

    /**
     * @return bool
     */
    public function isAdmin();

    /**
     * @return bool
     */
    public function isTrackChair();

    /**
     * @param string $token
     * @return bool
     * @throws EntityValidationException
     */
    public function doEmailConfirmation($token);

    /**
     * @return String
     */
    public function generateEmailVerificationToken();

    /**
     * @return $this
     */
    public function resetConfirmation();

    /**
     * @return string
     */
    public function getNameSlug();

    /**
     * @return bool
     */
    public function isProfileUpdated();

    /**
     * @return void
     */
    public function activate();

    /**
     * @return void
     */
    public function deActivate();

    /**
     * @return bool
     */
    public function isActive();
}