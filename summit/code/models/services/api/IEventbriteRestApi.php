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
interface IEventbriteRestApi
{

    /**
     * @param array $auth_info
     * @return $this
     */
    public function setCredentials(array $auth_info);

    /**
     * @param string $api_url
     * @param array $params
     * @return mixed
     */
    public function getEntity($api_url, array $params);

    /**
     * @param string $order_id
     * @return mixed
     */
    public function getOrder($order_id);
}