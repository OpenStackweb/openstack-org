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
/**
 * Class SummitPushNotificationManager
 */
final class SummitPushNotificationManager
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
     * @var IPushNotificationApi
     */
    private $push_api;

    /***
     * SummitPushNotificationManager constructor.
     * @param IEntityRepository $notifications_repository
     * @param IPushNotificationApi $push_api
     * @param ITransactionManager $tx_manager
     */
    public function __construct
    (
        IEntityRepository $notifications_repository,
        IPushNotificationApi $push_api,
        ITransactionManager $tx_manager
    )
    {
        $this->notifications_repository = $notifications_repository;
        $this->push_api                 = $push_api;
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


            foreach($list as $notification)
            {
                if(empty($notification->Message)) continue;
                $data  = [
                    'id'         => intval($notification->ID),
                    'type'       => SummitPushNotification::PushType,
                    'body'       => $notification->Message,
                    'summit_id'  => intval($notification->SummitID),
                    'channel'    => $notification->Channel,
                    'created_at' => $notification->getTimestamp(),
                ];
                // Push to speakers
                $to = null;
                try
                {

                    switch($notification->Channel)
                    {
                        case 'SPEAKERS':
                        {
                            $to = ['speakers'];
                        }
                            break;
                        case 'ATTENDEES':
                        {
                            $to = ['attendees'];
                        }
                            break;
                        case 'MEMBERS':
                        {
                            $recipients = array();

                            foreach($notification->Recipients() as $m)
                            {
                                array_push($recipients, 'member_'.$m->ID);
                            }
                            $to = $recipients;
                        }
                            break;
                        case 'SUMMIT':
                        {
                            $to = ['summit_'.$notification->SummitID];
                        }
                            break;
                        case 'EVENT':
                        {
                            $extra_data     = [
                                'event_id' => $notification->EventID,
                                'title'    => $notification->Event()->getTitle(),
                            ];

                            $data = array_merge($data, $extra_data);
                            $to   = ['event_'.$notification->EventID];
                        }
                            break;
                        case 'EVERYONE':
                        {
                            $to = ['everyone'];
                        }
                            break;
                        case 'GROUP':
                        {
                            $recipients = array();
                            foreach($notification->Group()->Members() as $m)
                            {
                                array_push($recipients, 'member_'.$m->ID);
                            }
                            $to = $recipients;
                        }
                            break;
                    }

                    $res = $this->push_api->sendPush($to, $data);

                    if($res) {
                        $notification->sent();
                        ++$qty;
                    }
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