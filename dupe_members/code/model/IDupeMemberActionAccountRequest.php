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
 * Interface IDupeMemberActionAccountRequest
 */
interface IDupeMemberActionAccountRequest extends IEntity {

    /**
     * @return string
     */
    public function getConfirmationHash();

    /**
     * @return void
     */
    public function generateConfirmationHash();

    /**
     * @param ICommunityMember $member
     * @return void
     */
    public function registerDupeAccount(ICommunityMember $member);

    /**
     * @param ICommunityMember $member
     * @return void
     */
    public function registerPrimaryAccount(ICommunityMember $member);

    /**
     * @return ICommunityMember $member
     */
    public function getPrimaryAccount();

    /**
     * @return ICommunityMember
     */
    public function getDupeAccount();

    /**
     * @return bool
     */
    public function isVoid();

    /**
     * @param string $token
     * @return bool
     * @throws DuperMemberActionRequestVoid
     * @throws InvitationAlreadyConfirmedException
     */
    public function doConfirmation($token);
} 