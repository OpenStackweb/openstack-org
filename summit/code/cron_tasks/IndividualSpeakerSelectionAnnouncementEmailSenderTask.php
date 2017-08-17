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
            $speaker    = null;
            $role       = IPresentationSpeaker::RoleSpeaker;
            if (isset($_GET['member_id']))
            {
                $speaker =  PresentationSpeaker::get()->filter("MemberID", $_GET['member_id'])->first();
            }

            if (isset($_GET['summit_id']))
            {
                $summit = Summit::get()->byID(intval($_GET['summit_id']));
            }

            if (isset($_GET['role']))
            {
               if($_GET['role'] == 2)
                   $role       = IPresentationSpeaker::RoleModerator;
            }

            if(is_null($summit)) throw new Exception('summit_id is not valid!');
            if(is_null($speaker)) throw new Exception('member_id is not valid!');

            echo sprintf("sending individual mail for %s speaker as role %s", $speaker->getEmail(), $role).PHP_EOL;

            $processed1  = $this->manager->sendSelectionAnnouncementEmailForSpeaker($speaker, $summit, $role, false);

            $finish_time = time() - $init_time;
            echo 'processed records (speakers) ' . $processed1.' - time elapsed : '.$finish_time. ' seconds.'.PHP_EOL;

        }
        catch(Exception $ex)
        {
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
            echo sprintf("there was an error %s", $ex->getMessage()).PHP_EOL;
        }
    }
}