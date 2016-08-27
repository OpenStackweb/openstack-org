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
 * Interface ICommunityMember
 */
interface ICommunityMember extends IEntity
{
    /**
     * @return bool
     */
    public function isCommunityMember();

    /**
     * @return string
     */
    public function getFirstName();

    /**
     * @return string
     */
    public function getLastName();

    /**
     * @return string
     */
    public function getEmail();

    /**
     * @param string $first_name
     * @param string $last_name
     * @return void
     */
    public function updateCompleteName($first_name, $last_name);

    /**
     * @param string $email
     * @return void
     */
    public function updateEmail($email);

    /**
     * @param string $email
     * @return void
     */
    public function updateSecondEmail($email);

    /**
     * @param string $email
     * @return void
     */
    public function updateThirdEmail($email);

    /**
     * @param string $shirt_size
     * @param string $statement_interest
     * @param string $bio
     * @param string $gender
     * @param string $food_preference
     * @param string $other_food
     * @return void
     */
    public function updatePersonalInfo($shirt_size, $statement_interest, $bio, $gender, $food_preference, $other_food);

    /**
     * @param string $projects
     * @param string $other_projects
     * @return void
     */
    public function updateProjects($projects, $other_projects);

    /**
     * @param string $irc_handle
     * @param string $twitter_name
     * @param string $linkedin_profile
     * @return void
     */
    public function updateSocialInfo($irc_handle, $twitter_name, $linkedin_profile);

    /**
     * @param string $address
     * @param string $suburb
     * @param string $state
     * @param string $postcode
     * @param string $city
     * @param string $country
     * @return void
     */
    public function updateAddress($address, $suburb, $state, $postcode, $city, $country);

    /**
     * @param $photo_id
     * @return mixed
     */
    public function updateProfilePhoto($photo_id);

    /**
     * @param bool $show
     * @return void
     */
    public function updateShowDupesOnProfile($show);

    /**
     * @return bool
     */
    public function shouldShowDupesOnProfile();

}