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
interface IMemberManager
{
    /**
     * @param array $data
     * @param EditProfilePage $profile_page
     * @param IMessageSenderService $sender_service
     * @return Member
     */
    public function register(array $data, EditProfilePage $profile_page, IMessageSenderService $sender_service);

    /**
     * @param string $token
     * @param IMessageSenderService $sender_service
     * @throws NotFoundEntityException
     * @return Member
     */
    public function verify($token, IMessageSenderService $sender_service);

    /**
     * @param array $data
     * @param EditProfilePage $profile_page
     * @param IMessageSenderService $sender_service
     * @return Member
     * @throws Exception
     */
    public function registerMobile(array $data, EditProfilePage $profile_page, IMessageSenderService $sender_service);


    /**
     * Register an speaker and confirm the registration request if exists
     * @param array $data
     * @param EditProfilePage $profile_page
     * @param IMessageSenderService $sender_service
     * @return Member
     */
    public function registerSpeaker(array $data, EditProfilePage $profile_page, IMessageSenderService $sender_service);

    /**
     * @param $email
     * @param IMessageSenderService $sender_service
     * @throws NotFoundEntityException
     * @throws EntityValidationException
     * @return Member
     */
    public function resendEmailVerification($email, IMessageSenderService $sender_service);

    /**
     * @param Member $member
     * @param IMessageSenderService $sender_service
     * @return Member
     */
    public function resetEmailVerification(Member $member, IMessageSenderService $sender_service);

}