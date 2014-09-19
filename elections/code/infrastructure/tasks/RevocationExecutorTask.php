<?php

/**
 * Class RevocationExecutorTask
 */
final class RevocationExecutorTask extends CliController {

	function process(){

		set_time_limit(0);

		try{

			$batch_size = 1000;
			if(isset($_GET['batch_size'])){
				$batch_size = intval(trim(Convert::raw2sql($_GET['batch_size'])));
			}


			$manager = new RevocationNotificationManager(new SapphireFoundationMemberRepository,
				new SapphireFoundationMemberRevocationNotificationRepository,
				new SapphireElectionRepository,
				new RevocationNotificationFactory,
				SapphireTransactionManager::getInstance());

			$manager->revokeIgnoredNotifications($batch_size);

			return 'OK';
		}
		catch(Exception $ex){
			SS_Log::log($ex,SS_Log::ERR);
			echo $ex->getMessage();
		}
	}
} 