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

/**
 * Class EventBriteApiTest
 */
class EventBriteApiTest extends SapphireTest
{

    public function tearDown()
    {
        Mockery::close();
    }

    public function testGetOrders(){
        $client = new EventbriteRestApi();
        $client->setCredentials(array('token' => EVENTBRITE_PERSONAL_OAUTH2_TOKEN));
        $page = 1;
        $summit_mock = Mockery::mock(Summit::class);
        $summit_mock->shouldReceive("getExternalEventId")->andReturn("28375675409");
        $response = $client->getOrdersBySummit($summit_mock, $page);
        $this->assertTrue(isset($response['orders']));
        $this->assertTrue(count($response['orders']) > 0);
    }

    public function testGetTicketTypes(){
        $client = new EventbriteRestApi();
        $client->setCredentials(array('token' => EVENTBRITE_PERSONAL_OAUTH2_TOKEN));
        $summit_mock = Mockery::mock(Summit::class);
        $summit_mock->shouldReceive("getExternalEventId")->andReturn("28375675409");
        $response = $client->getTicketTypes($summit_mock);
        $this->assertTrue(isset($response['ticket_classes']));
        $this->assertTrue(count($response['ticket_classes']) > 0);
    }
}