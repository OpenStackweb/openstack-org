<?php

/**
 * Class RevocationNotificationSenderTask
 */
final class RevocationNotificationSenderTask extends CliController {

	function process(){

		set_time_limit(0);

		try{

			$batch_size = 100;
			if(isset($_GET['batch_size'])){
				$batch_size = intval(trim(Convert::raw2sql($_GET['batch_size'])));
			}
			$max_past_elections = 2 ;

			$manager = new RevocationNotificationManager(new SapphireFoundationMemberRepository,
				new SapphireFoundationMemberRevocationNotificationRepository,
				new SapphireElectionRepository,
				new RevocationNotificationFactory,
				SapphireTransactionManager::getInstance());

			$manager->sendOutNotifications($max_past_elections, $batch_size, new RevocationNotificationEmailSender);

			return 'OK';
		}
		catch(Exception $ex){
			SS_Log::log($ex,SS_Log::ERR);
			echo $ex->getMessage();
		}
	}
} 