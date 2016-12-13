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
final class SpeakerUploadSlidesEmailSenderTask extends CronTask
{

    /**
     * @return void
     */
    public function run()
    {
        try
        {
            $batch_size = 100;
            $init_time  = time();
            $summit     = null;

            if (isset($_GET['batch_size']))
            {
                $batch_size = intval(trim(Convert::raw2sql($_GET['batch_size'])));
                echo sprintf('batch_size set to %s', $batch_size).PHP_EOL;
            }

            if (isset($_GET['summit_id']))
            {
                $summit = Summit::get()->byID(intval($_GET['summit_id']));
            }

            if(is_null($summit)) throw new Exception('summit_id is not valid!');

            $manager = Injector::inst()->get('SpeakerEmailAnnouncementSenderManager');
            if (!$manager instanceof ISpeakerEmailAnnouncementSenderManager) {
                return;
            }

            $processed  = $manager->sendUploadSlidesAnnouncementBySummit($summit, $batch_size);

            $finish_time = time() - $init_time;
            echo 'processed records (speakers) ' . $processed.' processed records (speakers) ' . $processed. ' - time elapsed : '.$finish_time. ' seconds.'.PHP_EOL;

        }
        catch(Exception $ex)
        {
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
            echo sprintf("there was an error %s", $ex->getMessage()).PHP_EOL;
        }
    }
}