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
class EventbriteOrderPlacedEndpoint extends AbstractRestfulJsonApi
{

    const ApiPrefix = 'api/v1/eventbrite';

    /**
     * @var IEventbriteEventManager
     */
    private $manager;

    public function getEventbriteEventManager()
    {
        return $this->manager;
    }

    public function setEventbriteEventManager(IEventbriteEventManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @return bool
     */
    protected function authorize()
    {
        return true;
    }

    protected function authenticate()
    {
        return true;
    }

    protected function isApiCall()
    {
        $request = $this->getRequest();
        if(is_null($request)) return false;
        return  strpos(strtolower($request->getURL()),self::ApiPrefix) !== false;
    }

    static $url_handlers = array
    (
        'POST place-order' => 'placeOrder',
    );

    static $allowed_actions = array
    (
        'placeOrder',
    );

    public function placeOrder(SS_HTTPRequest $request)
    {
        $eventbrite_event_header = $request->getHeader('X-Eventbrite-Event');
        if(!$eventbrite_event_header) return $this->httpError(403);
        if($eventbrite_event_header !== 'order.placed') return $this->httpError(403);
        if(!$this->isJson()) return $this->httpError(403);

        $json_request = $this->getJsonRequest();
        if(!isset($json_request['config']) || !isset($json_request['api_url'])) return $this->httpError(403);
        $config = $json_request['config'];
        if(!isset($config['action']) || $config['action']!== 'order.placed') return $this->httpError(403);
        $current_local_url = Controller::join_links(Director::absoluteBaseURL(), $request->getURL());
        if(!isset($config['endpoint_url']) || $config['endpoint_url']!== $current_local_url) return $this->httpError(403);

        try
        {
            $this->manager->registerEvent('ORDER_PLACED', $json_request['api_url']);
        }
        catch(Exception $ex)
        {
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
            return $this->httpError(500);
        }
        return true;
    }
}