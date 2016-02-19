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
final class SummitEventSetAvgRateTask extends CronTask
{

    /**
     * @return void
     */
    public function run()
    {
        try
        {
            $init_time  = time();
            $processed_events = 0;
            $events = array();

            $current_summit = Summit::get_active();
            if($current_summit) {
                $events = $current_summit->getSchedule();
            }

            foreach ($events as $event) {

                $rate_sum = 0;
                $rate_count = 0;
                foreach ($event->Feedback as $feedback) {
                    $rate_count++;
                    $rate_sum = $rate_sum + $feedback->Rate;
                }

                $avg_rate = ($rate_count > 0) ? ($rate_sum/$rate_count) : 0;

                try {
                    $avg_rate = round($avg_rate,2);
                    $old_avg_rate = $event->getAvgRate();
                    if(floatval($avg_rate) === floatval($old_avg_rate)) continue;
                    $event->setAvgRate($avg_rate);
                    $event->write(true);
                    $processed_events++;
                } catch (Exception $ex) {
                    SS_Log::log($ex, SS_Log::ERR);
                    echo $ex->getMessage();
                }

            }

            $finish_time = time() - $init_time;
            echo 'processed events ' . $processed_events. ' - time elapsed : '.$finish_time. ' seconds.';
        }
        catch(Exception $ex)
        {
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
        }
    }
}