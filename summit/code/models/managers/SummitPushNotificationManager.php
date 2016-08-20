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
use Parse\ParseClient;
use Parse\ParsePush;
use Parse\ParseInstallation;

class SummitPushNotificationManager
{
    /**
     * @var ITransactionManager
     */
    private $tx_manager;

    /**
     * @var IEntityRepository
     */
    private $notifications_repository;

    /**
     * SummitPushNotificationManager constructor.
     * @param IEntityRepository $notifications_repository
     * @param ITransactionManager $tx_manager
     */
    public function __construct
    (
        IEntityRepository $notifications_repository,
        ITransactionManager $tx_manager)
    {
        $this->notifications_repository = $notifications_repository;
        $this->tx_manager               = $tx_manager;
    }

    public function processNotifications($batch_size)
    {
        $notifications_repository = $this->notifications_repository;

        return $this->tx_manager->transaction(function() use($batch_size, $notifications_repository)
        {
            $qty   = 0;
            $query = new QueryObject();
            $query->addAndCondition(QueryCriteria::equal('IsSent', 0));
            list($list, $size)  = $notifications_repository->getAll($query, 0, $batch_size);
            // init parse ...
            ParseClient::initialize( PARSE_APP_ID, PARSE_REST_KEY, PARSE_MASTER_KEY);

            foreach($list as $notification)
            {
                if(empty($notification->Message)) continue;
                $message  = [

                    "data" => [
                        'alert'     => $notification->Message,
                        'id'        => intval($notification->ID),
                        'summit_id' => intval($notification->SummitID),
                        'type'      => $notification->Channel,
                    ]
                ];
                // Push to speakers
                try
                {

                    switch($notification->Channel)
                    {
                        case 'SPEAKERS':
                        {
                            $message['channels'] = ['speakers'];
                        }
                            break;
                        case 'ATTENDEES':
                        {
                            $message['channels'] = ['attendees'];
                        }
                            break;
                        case 'MEMBERS':
                        {
                            $recipients = array();

                            foreach($notification->Recipients() as $m)
                            {
                                array_push($recipients, 'me_'.$m->ID);
                            }
                            $message['channels'] = $recipients;
                        }
                            break;
                        case 'SUMMIT':
                        {
                            $message['channels'] = ['su_'.$notification->SummitID];
                        }
                            break;
                        case 'EVENT':
                        {
                            $data           = $message['data'];
                            $extra_data     = [
                                'event_id' => $notification->EventID,
                                'title'    => $notification->Event()->getTitle(),
                            ];

                            $message['data']     = array_merge($data, $extra_data);
                            $message['channels'] = ['evt_'.$notification->EventID];
                        }
                            break;
                        case 'EVERYONE':
                        {
                            $message['where'] = ParseInstallation::query();
                        }
                            break;
                    }
                    ParsePush::send($message);
                    $notification->sent();
                    ++$qty;
                }
                catch(Exception $ex)
                {
                    SS_Log::log($ex->getMessage(), SS_Log::ERR);
                }
            }

            return $qty;
        });
    }
}