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
     * @param Member $member
     * @param array $data
     * @param IMessageSenderService $sender_service
     * @return Member
     * @throws EntityValidationException
     */
    public function register(Member $member, array $data):Member;

    /**
     * @param Member $member
     * @return void
     */
    public function activate(Member $member);

    /**
     * @param Member $member
     * @return void
     */
    public function deactivate(Member $member);

    /**
     * @param mixed $claims
     * @return Member
     */
    public function registerByClaims($claims):Member;

}