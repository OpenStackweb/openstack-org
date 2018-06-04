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
final class EventbriteSummitOrderConciliation extends CronTask
{

    /**
     * @var IEventbriteEventManager
     */
    private $manager;

    /**
     * EventbriteSummitOrderConciliation constructor.
     * @param IEventbriteEventManager $manager
     */
    public function __construct(IEventbriteEventManager $manager)
    {
        parent::__construct();
        $this->manager = $manager;
    }

    /**
     * @return void
     */
    public function run()
    {
        try
        {
            $init_time  = time();
            foreach(Summit::get_not_finished() as $summit) {
                if(empty($summit->ExternalEventId)){
                    echo sprintf("skipping summit id %s bc ExternalEventId is not set", $summit->ID).PHP_EOL;
                }
                echo sprintf("conciliating order for summit id %s - ExternalEventId %s", $summit->ID, $summit->ExternalEventId).PHP_EOL;
                $this->manager->conciliateEventbriteOrders($summit);
            }
            $finish_time = time() - $init_time;
            echo 'time elapsed : '.$finish_time. ' seconds.'.PHP_EOL;
        }
        catch(Exception $ex)
        {
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
        }
    }
}