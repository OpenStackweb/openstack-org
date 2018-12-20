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

/**
 * Interface IEntitySurvey
 */
interface IEntitySurvey extends ISurvey {

    /**
     * @return ISurvey
     */
    public function parent();

    /**
     * @return ISurveyDynamicEntityStep
     */
    public function owner();

    /**
     * @return string
     */
    public function getFriendlyName();

    /**
     * @return bool
     */
    public function isTeamEditionAllowed();

    /**
     * @param Member $member
     * @return void
     */
    public function addTeamMember(Member $member);

    /**
     * @param Member $member
     * @return void
     */
    public function removeTeamMember(Member $member);

    /**
     * @return Member[]
     */
    public function getTeamMembers();

    /**
     * @return Member
     */
    public function getUpdateBy();

    /**
     * @return bool
     */
    public function iAmOwner();

    /**
     * @param Member $member
     * @return bool
     */
    public function isTeamMember(Member $member);

    /**
     * @return string|null
     */
    public function getOrganization();

    /**
     * @param Member $member
     * @return bool
     */
    public function hasTeamPermissions(Member $member);
}