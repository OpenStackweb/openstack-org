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
final class PushNotificationSenderTask extends CronTask
{

    /**
     * @var IFireBasePushNotificationSerializationStrategyFactory
     */
    private $serialization_strategy_factory;

    /**
     * PushNotificationSenderTask constructor.
     * @param IFireBasePushNotificationSerializationStrategyFactory $serialization_strategy_factory
     */
    public function __construct(IFireBasePushNotificationSerializationStrategyFactory $serialization_strategy_factory)
    {
        parent::__construct();
        $this->serialization_strategy_factory = $serialization_strategy_factory;
    }

    /**
     * @return void
     */
    public function run()
    {
        try
        {

            $init_time  = time();
            $batch_size = 1000;
            if (isset($_GET['batch_size']))
            {
                $batch_size = intval(trim(Convert::raw2sql($_GET['batch_size'])));
                echo sprintf('batch_size set to %s', $batch_size);
            }

            $manager   = new PushNotificationManager
            (
                new SapphirePushNotificationRepository,
                new FireBaseGCMApi(FIREBASE_GCM_SERVER_KEY),
                $this->serialization_strategy_factory,
                SapphireTransactionManager::getInstance()
            );

            $processed = $manager->processNotifications($batch_size);
            $finish_time = time() - $init_time;
            echo 'processed records ' . $processed. ' - time elapsed : '.$finish_time. ' seconds.';
        }
        catch(Exception $ex)
        {
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
        }
    }
}