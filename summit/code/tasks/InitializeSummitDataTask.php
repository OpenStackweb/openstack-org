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
class InitializeSummitDataTask extends BuildTask
{
    /**
     * @var string $title Shown in the overview on the {@link TaskRunner}
     * HTML or CLI interface. Should be short and concise, no HTML allowed.
     */
    protected $title = "Initialize Summit Data Task";

    /**
     * @var string $description Describe the implications the task has,
     * and the changes it makes. Accepts HTML formatting.
     */
    protected $description = 'Initialize Summit Data Task';


    /**
     * Implement this method in the task subclass to
     * execute via the TaskRunner
     */
    public function run($request)
    {
        set_time_limit(0);

        $summit_id = intval($request->getVar('summit_id'));
        if(empty($summit_id)) throw new InvalidArgumentException('missing summit_id param!');


        $summit_external_id = intval($request->getVar('summit_external_id'));
        if(empty($summit_external_id)) throw new InvalidArgumentException('missing summit_external_id param!');

        // get ticket types ...

        $summit = Summit::get()->byID($summit_id);
        if(is_null($summit)) throw new InvalidArgumentException("non existent summit!");


        $summit->ExternalEventId = $summit_external_id;
        $summit->write();

        Summit::seedBasicEventTypes($summit_id);


        $client   = new GuzzleHttp\Client();

        $query = array
        (
            'token' => EVENTBRITE_PERSONAL_OAUTH2_TOKEN,
        );


        $response = $client->get(sprintf("https://www.eventbriteapi.com/v3/events/%s/ticket_classes/", $summit_external_id), array
            (
                'query' => $query
            )
        );

        if($response->getStatusCode() !== 200) throw new Exception('invalid status code!');
        $content_type = $response->getHeader('content-type');
        if(empty($content_type)) throw new Exception('invalid content type!');
        if($content_type !== 'application/json') throw new Exception('invalid content type!');

        $json         = $response->getBody()->getContents();
        $ticket_types = json_decode($json, true);
        $ticket_types = $ticket_types['ticket_classes'];

        foreach($ticket_types as $t)
        {
            $new_ticket = new SummitTicketType();
            $new_ticket->ExternalId = $t["id"];
            if(SummitTicketType::get()->filter( array( 'SummitID' => $summit_id , 'ExternalId' => $new_ticket->ExternalId))->count() > 0) continue;
            $new_ticket->Name = $t["name"];
            $new_ticket->Description = $t["description"];
            $new_ticket->SummitID = $summit_id;
            $new_ticket->write();
        }


    }
}