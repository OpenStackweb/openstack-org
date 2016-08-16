<?php

/**
 * Copyright 2016 OpenStack Foundation
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
interface IJobRegistrationRequestManager
{
    /**
     * @param array $data
     * @return IJob
     */
    public function registerJobRegistrationRequest(array $data);

    /**
     * @param array $data
     * @return IJobRegistrationRequest
     */
    public function updateJobRegistrationRequest(array $data);

    /**
     * @param $id
     * @param $jobs_link
     * @return IJob
     */
    public function postJobRegistrationRequest($id, $jobs_link);

    /**
     * @param int $batch_size
     * @param string $email_alert_to
     * @param string $details_url
     */
    public function makeDigest($batch_size = 15, $email_alert_to, $details_url);

    /**
     * @param string $period
     * @return mixed
     */
    public function makePurge($period='P1Y');

    /**
     * @param $id
     * @param array $data
     * @param $jobs_link
     * @return mixed
     */
    public function rejectJobRegistration($id, array $data, $jobs_link);
}