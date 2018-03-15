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

final class IndividualSpeakerSelectionAnnouncementEmailSenderTask extends CronTask
{

    /**
     * @var ISpeakerEmailAnnouncementSenderManager
     */
    private $manager;

    /**
     * SpeakerSelectionAnnouncementEmailSenderTask constructor.
     * @param ISpeakerEmailAnnouncementSenderManager $manager
     */
    public function __construct(ISpeakerEmailAnnouncementSenderManager $manager)
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
            $summit     = null;
            $speakers   = [];

            if (isset($_GET['speaker_ids']))
            {
                $speakers_ids   = $_GET['speaker_ids'];
                foreach (explode(',', $speakers_ids) as $speaker_id){
                    $speaker = PresentationSpeaker::get()->byID($speaker_id);
                    if(is_null($speaker)) continue;
                    $speakers[] = $speaker;
                }
            }

            if (isset($_GET['summit_id']))
            {
                $summit = Summit::get()->byID(intval($_GET['summit_id']));
            }

            if(is_null($summit)) throw new Exception('summit_id is not valid!');
            if(count($speakers) == 0) throw new Exception('speakers set is empty valid!');
            $processed = 0;

            foreach($speakers as $speaker) {
                $role = $speaker->isModeratorFor($summit) ? IPresentationSpeaker::RoleModerator : IPresentationSpeaker::RoleSpeaker;

                echo sprintf("sending individual mail for %s speaker as role %s", $speaker->getEmail(), $role) . PHP_EOL;
                $processed += $this->manager->sendSelectionAnnouncementEmailForSpeaker($speaker, $summit, $role, false);
            }

            $finish_time = time() - $init_time;
            echo 'processed records (speakers) ' . $processed . ' - time elapsed : ' . $finish_time . ' seconds.' . PHP_EOL;
        }
        catch(Exception $ex)
        {
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
            echo sprintf("there was an error %s", $ex->getMessage()).PHP_EOL;
        }
    }
}