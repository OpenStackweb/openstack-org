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
final class EventbriteSummitTicketTypeConciliation extends CronTask
{

    /**
     * @return void
     */
    public function run()
    {
        try {
            $init_time = time();
            $api = new EventbriteRestApi();
            $api->setCredentials(array('token' => EVENTBRITE_PERSONAL_OAUTH2_TOKEN));

            if (!isset($_GET['summit_external_id'])) {
                echo "you must provide a summit_external_id!" . PHP_EOL;
                return -1;
            }

            $repository = new SapphireSummitRepository();
            $summit = $repository->getByExternalEventId($_GET['summit_external_id']);
            if (is_null($summit)) {
                echo "summit not found!" . PHP_EOL;
                return -1;
            }

            $count = SapphireTransactionManager::getInstance()->transaction(function () use ($summit, $api) {

                $count = 0;
                $response = $api->getTicketTypes($summit);
                if (!isset($response['ticket_classes'])) return;

                $ticket_classes = $response['ticket_classes'];

                foreach ($ticket_classes as $ticket_class) {

                    $id              = $ticket_class['id'];
                    $old_ticket_type = SummitTicketType::get()->filter(['SummitID' => $summit->ID, 'ExternalId' => $id])->first();

                    if (!is_null($old_ticket_type)) {

                        $old_ticket_type->Name        = trim($ticket_class['name']);
                        $old_ticket_type->Description = isset($ticket_class['description']) ? trim($ticket_class['description']) : '';
                        $old_ticket_type->write();

                        echo sprintf("ticket type %s already exists for summit %s", $id, $summit->ID) . PHP_EOL;
                        continue;
                    }

                    $new_ticket_type              = new SummitTicketType();
                    $new_ticket_type->SummitID    = $summit->ID;
                    $new_ticket_type->ExternalId  = $id;
                    $new_ticket_type->Name        = trim($ticket_class['name']);
                    $new_ticket_type->Description = isset($ticket_class['description']) ? trim($ticket_class['description']) : '';
                    $new_ticket_type->write();

                    echo sprintf("ticket type %s (%s) created for summit %s", $ticket_class['name'], $id, $summit->ID) . PHP_EOL;
                    ++$count;
                }

                return $count;
            });

            $finish_time = time() - $init_time;

            echo sprintf("a total of %s ticket type were added to summit %s", $count, $summit->ID) . PHP_EOL;

            echo sprintf('time elapsed : %s seconds', $finish_time) . PHP_EOL;

        }
        catch(Exception $ex)
        {
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
        }
    }
}