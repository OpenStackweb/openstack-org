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
    /**
     * @param string $to
     * @param array $data
     * @param string $priority
     * @param string $platform
     * @param null|int $ttl
     * @return bool
     */
    function sendPush($to, array $data, $priority = IPushNotificationApi::NormalPriority, $platform = 'MOBILE', $ttl = null);

    /**
     * https://developers.google.com/instance-id/reference/server#create_relationship_maps_for_app_instances
     * Given a registration token and a supported relationship, you can create a mapping. For example, you can
     * subscribe an app instance to a Google Cloud Messaging topic by calling the Instance ID service at this endpoint,
     * providing the app instance's token as shown:
     * @param string $token
     * @param string $topic
     * @return bool
     */
    function subscribeToTopic($token, $topic);
}