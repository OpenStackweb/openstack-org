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
class SchedImportDataPerSummitTask extends BuildTask
{

    /**
     * @var string $title Shown in the overview on the {@link TaskRunner}
     * HTML or CLI interface. Should be short and concise, no HTML allowed.
     */
    protected $title = "Sched Import Data Per Summit";

    /**
     * @var string $description Describe the implications the task has,
     * and the changes it makes. Accepts HTML formatting.
     */
    protected $description = 'Sched Import Data Per Summit';



    /**
     * Implement this method in the task subclass to
     * execute via the TaskRunner
     */
    public function run($request)
    {
        set_time_limit(0);

        $summit_id = intval($request->getVar('summit_id'));
        if(empty($summit_id)) throw new InvalidArgumentException('missing summit_id param!');


        $client   = new GuzzleHttp\Client();

        $query = array
        (
            'api_key' => SCHED_API_KEY,
            'format' => 'json',
        );



        $response = $client->get("http://openstacksummitoctober2015tokyo.sched.org/api/session/list", array
            (
                'query' => $query
            )
        );

        if($response->getStatusCode() !== 200) throw new Exception('invalid status code!');
        $content_type = $response->getHeader('content-type');
        if(empty($content_type)) throw new Exception('invalid content type!');
        if($content_type !== 'application/json') throw new Exception('invalid content type!');

        $json   = $response->getBody()->getContents();
        $events = json_decode($json, true);

        foreach($events as $sched_event)
        {
            if($sched_event["active"] !== "Y") continue;

            $title      = $sched_event["name"];
            $start_date = $sched_event["event_start"];
            $end_date   = $sched_event["event_end"];
            $event_type = isset($sched_event["event_type"]) ? $sched_event["event_type"] : $title;
            $venue      = $sched_event["venue"];

            $event    = SummitEvent::get()->filter( array("SummitID" => $summit_id, "Title" => trim($title)))->first();
            $location = SummitVenueRoom::get()->filter( array("SummitID" => $summit_id, "Name" => trim($venue)))->first();
            if(is_null($location) && $venue !== 'TBA')
            {
                // create location
                $main_venue = SummitVenue::get()->filter( array("SummitID" => $summit_id))->first();
                $location = new SummitVenueRoom();
                $location->SummitID = $summit_id;
                $location->Name = trim($venue);
                $location->VenueID = $main_venue->ID;
                $location->write();
            }

            if(is_null($event))
            {
                // create event and event type ...

                if(isset($sched_event['speakers'])) continue;

                $type = SummitEventType::get()->filter( array( "SummitID" => $summit_id, "Type" => trim($event_type)))->first();

                if(is_null($type))
                {
                    $type = new SummitEventType();
                    $type->SummitID = $summit_id;
                    $type->Type = trim($event_type);
                    $type->write();
                }

                $event           = new SummitEvent();
                $event->TypeID   = $type->ID;
                $event->SummitID = $summit_id;
                $event->Title    = trim($title);
            }

            if($event->isPublished()) continue;

            $event->StartDate  = $start_date;
            $event->EndDate    = $end_date;
            if(!is_null($location))
                $event->LocationID = $location->ID;
            try
            {
                $event->publish();
                $event->write();
            }
            catch(Exception $ex)
            {
                SS_Log::log($ex->getMessage(), SS_Log::ERR);
            }
        }
    }
}