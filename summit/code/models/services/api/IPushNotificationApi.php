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
interface IPushNotificationApi
{
    const NormalPriority = 'normal';
    const HighPriority   = 'high';

    /**
     * @param string $to
     * @param array $data
     * @param string $priority
     * @param null|int $ttl
     * @return bool
     */
    function sendPush($to,  array $data, $priority = self::NormalPriority, $ttl = null);

    /**
     * @param string $to
     * @param array $data
     * @param string $priority
     * @param null|int $ttl
     * @return bool
     */
    function sendPushMobile($to, array $data, $priority = IPushNotificationApi::NormalPriority, $ttl = null);

    /**
     * @param string $to
     * @param array $data
     * @param string $priority
     * @param null|int $ttl
     * @return bool
     */
    function sendPushWeb($to, array $data, $priority = IPushNotificationApi::NormalPriority, $ttl = null);

    /**
     * @param string $token
     * @param string $topic
     * @return bool
     */
    function subscribeToTopicWeb($token, $topic);
}