<?php
/**
 * Copyright 2014 Openstack Foundation
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
 * Class RevocationNotificationSenderTask
 */
final class RevocationNotificationSenderTask extends CronTask {

	function run(){

		try{

			$batch_size         = 100;
            $max_past_elections = 2 ;

			if(isset($_GET['batch_size'])){
				$batch_size = intval(trim(Convert::raw2sql($_GET['batch_size'])));
			}

			$manager = new RevocationNotificationManager(new SapphireFoundationMemberRepository,
				new SapphireFoundationMemberRevocationNotificationRepository,
				new SapphireElectionRepository,
				new RevocationNotificationFactory,
				SapphireTransactionManager::getInstance());

			$count = $manager->sendOutNotifications
            (
                $max_past_elections,
                $batch_size,
                new RevocationNotificationEmailSender
            );

			echo sprintf("processed %s records .", $count).PHP_EOL;
		}
		catch(Exception $ex){
			SS_Log::log($ex,SS_Log::ERR);
			echo $ex->getMessage();
		}
	}
} 