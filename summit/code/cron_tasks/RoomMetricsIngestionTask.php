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
final class RoomMetricsIngestionTask extends CronTask
{

    /**
     * @return void
     */
    public function run()
    {
        try
        {

            $init_time  = time();
            $summit_id  = Summit::ActiveSummitID();

            if (isset($_GET['summit_id']))
            {
                $summit_id = intval(trim(Convert::raw2sql($_GET['summit_id'])));
                echo sprintf('summit_id set to %s', $summit_id).PHP_EOL;
            }

            $manager = Injector::inst()->get('SummitVenueRoomMetricsManager');
            $manager->ingest($summit_id);

            $finish_time = time() - $init_time;
            echo 'time elapsed : '.$finish_time. ' seconds.'.PHP_EOL;
        }
        catch(Exception $ex)
        {
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
        }
    }
}