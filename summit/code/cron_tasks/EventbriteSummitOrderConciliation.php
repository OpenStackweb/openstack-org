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
class EventbriteSummitOrderConciliation extends CronTask
{

    /**
     * @return void
     */
    public function run()
    {
        $api = new EventbriteRestApi();
        $api->setCredentials(array('token' => EVENTBRITE_PERSONAL_OAUTH2_TOKEN));

        if (!isset($_GET['summit_external_id']))
        {
            echo "you must provide a summit_external_id!".PHP_EO;
            return -1;
        }

        $repository = new SapphireSummitRepository();
        $summit     = $repository->getByExternalEventId($_GET['summit_external_id']);
        if(is_null($summit)){
            echo "summit not found!".PHP_EOL;
            return -1;
        }

        SapphireTransactionManager::getInstance()->transaction(function() use($summit, $api ){

            $page    = 1;
            $process = true;
            do {

                $response = $api->getOrdersBySummit($summit, $page);
                if(!isset($response['pagination'])) break;
                if(!isset($response['orders'])) break;

                $page_info  = $response['pagination'];
                $page_count = $page_info['page_count'];
                $orders     = $response['orders'];

                echo sprintf("processing page %s of %s", $page, $page_count).PHP_EOL;

                foreach($orders as $order){

                    $uri       = $order['resource_uri'];
                    $api_event = EventbriteEvent::get()->filter('ApiUrl', $uri)->first();

                    if(is_null($api_event)){
                        $api_event = new EventbriteEvent();
                        $api_event->ApiUrl      = $uri;
                        $api_event->EventType   = 'ORDER_PLACED';
                        $api_event->FinalStatus =  $order['status'];
                    }

                    $api_event->SummitID = $summit->getIdentifier();
                    $api_event->write();

                    DB::query("DELETE FROM EventbriteAttendee WHERE EventbriteOrderID = {$api_event->ID};");

                    foreach($order['attendees']as $attendee){
                        $profile     = $attendee['profile'];
                        $costs       = $attendee['costs'];
                        $db_attendee = new EventbriteAttendee();
                        $db_attendee->FirstName             = $profile['first_name'];
                        $db_attendee->LastName              = $profile['last_name'];
                        $db_attendee->Email                 = $profile['email'];
                        $db_attendee->EventbriteOrderID     = $api_event->ID;
                        $db_attendee->Price                 = $costs['base_price']['major_value'];
                        $db_attendee->ExternalAttendeeId    = $attendee['id'];
                        $db_attendee->ExternalTicketClassId = $attendee['ticket_class_id'];
                        $db_attendee->Status                = $attendee['status'];
                        $db_attendee->write();
                    }
                }

                ++$page;
                if($page > $page_count) $process = false;
            }
            while($process);
        });

    }
}