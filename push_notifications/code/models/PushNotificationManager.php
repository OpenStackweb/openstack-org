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
 * Class PushNotificationManager
 */
class PushNotificationManager
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

    /**
     * @var IFireBasePushNotificationSerializationStrategyFactory
     */
    private $serialization_strategy_factory;

    /**
     * PushNotificationManager constructor.
     * @param IEntityRepository $notifications_repository
     * @param IPushNotificationApi $push_api
     * @param IFireBasePushNotificationSerializationStrategyFactory $serialization_strategy_factory
     * @param ITransactionManager $tx_manager
     */
    public function __construct
    (
        IEntityRepository $notifications_repository,
        IPushNotificationApi $push_api,
        IFireBasePushNotificationSerializationStrategyFactory $serialization_strategy_factory,
        ITransactionManager $tx_manager
    )
    {
        $this->notifications_repository        = $notifications_repository;
        $this->push_api                        = $push_api;
        $this->serialization_strategy_factory  = $serialization_strategy_factory;
        $this->tx_manager                      = $tx_manager;
    }

    public function processNotifications($batch_size)
    {

        return $this->tx_manager->transaction(function() use($batch_size)
        {
            $qty   = 0;
            $query = new QueryObject();
            $query->addAndCondition(QueryCriteria::equal('IsSent', 0));
            $query->addAndCondition(QueryCriteria::equal('Approved', 1));
            list($list, $size)  = $this->notifications_repository->getAll($query, 0, $batch_size);
            // init parse ...

            foreach($list as $notification)
            {
                if(empty($notification->Message)) continue;

                try
                {
                    $serializer = $this->serialization_strategy_factory->build($notification);
                    if(is_null($serializer)) continue;

                    $res = $this->push_api->sendPush
                    (
                        $serializer->getToField(),
                        $serializer->getDataField(),
                        IPushNotificationMessage::HighPriority,
                        $notification->getPlatform()
                    );

                    if($res) {
                        $notification->sent();
                        ++$qty;
                    }
                }
                catch (InvalidArgumentException $ex1){
                    SS_Log::log($ex1->getMessage(), SS_Log::WARN);
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