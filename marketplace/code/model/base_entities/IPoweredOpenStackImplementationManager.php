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
interface IPoweredOpenStackImplementationManager
{
    /**
     * @param array $data
     * @param int $service_id
     * @throws NotFoundEntityException
     * @return void
     */
    function updatePoweredProgram(array $data, $service_id);

    /**
     * @param IMessageSenderService $service
     * @param int $days_about_expire_on
     * @return void
     */
    function sendExpiredPoweredProgramDigest(IMessageSenderService $service, $days_about_expire_on);

    /**
     * @param array $data
     * @param string $type
     * @param int $service_id
     * @return int
     * @throws NotFoundEntityException
     */
    function createImplementationLink(array $data, $type, $service_id);

    /**
     * @param string $type
     * @param int $link_id
     * @param int $service_id
     * @return void
     * @throws NotFoundEntityException
     */
    function deleteImplementationLink($type, $link_id, $service_id);
}