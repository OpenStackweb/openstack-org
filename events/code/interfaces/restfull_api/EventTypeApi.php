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
 * Class AbstractRestfulJsonApi
 */

class EventTypeApi
    extends AbstractRestfulJsonApi {

    private $event_manager;

    public function __construct(){
        parent::__construct();
        $this->event_manager = new EventManager(
            $this->repository,
            new EventRegistrationRequestFactory,
            null,
            new SapphireEventPublishingService,
            new EventValidatorFactory,
            SapphireTransactionManager::getInstance()
        );
    }

    const ApiPrefix = 'api/v1/events-types';

    protected function isApiCall()
    {
        $request = $this->getRequest();
        if (is_null($request)) return false;
        return strpos(strtolower($request->getURL()), self::ApiPrefix) !== false;
    }

    /**
     * @return bool
     */
    protected function authorize() {
        return true;
    }

    /**
     * @return bool
     */
    protected function authenticate() {
        return true;
    }

    /**
     * @var array
     */
    static $url_handlers = array(
        'GET count-by-type' => 'countByType'
    );

    /**
     * @var array
     */
    static $allowed_actions = array(
        'countByType',
    );

    public function countByType() {
        $countByEventType = $this->event_manager->getCountByType();
        return $this->ok($countByEventType);
    }
}