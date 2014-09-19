<?php

/**
 * Class JobRegistrationRequestPurgeTask
 */
final class JobRegistrationRequestPurgeTask extends CliController {

	function process(){

		set_time_limit(0);

		try{
			$manager  = new JobRegistrationRequestManager(
				new SapphireJobRegistrationRequestRepository,
				new SapphireJobRepository,
				new SapphireJobAlertEmailRepository,
				new JobFactory,
				new JobsValidationFactory,
				new SapphireJobPublishingService,
				SapphireTransactionManager::getInstance()
			);

			$manager->makePurge();

			return 'OK';
		}
		catch(Exception $ex){
			SS_Log::log($ex,SS_Log::ERR);
			echo $ex->getMessage();
		}
	}

} 