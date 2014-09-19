<?php

/**
 * Class JobRegistrationRequestDigestTask
 */
final class JobRegistrationRequestDigestTask extends CliController {

	function process(){

		set_time_limit(0);

		try{

			$batch_size = 15;
			if(isset($_GET['batch_size'])){
				$batch_size = intval(trim(Convert::raw2sql($_GET['batch_size'])));
			}
			$manager  = new JobRegistrationRequestManager(
				new SapphireJobRegistrationRequestRepository,
				new SapphireJobRepository,
				new SapphireJobAlertEmailRepository,
				new JobFactory,
				new JobsValidationFactory,
				new SapphireJobPublishingService,
				SapphireTransactionManager::getInstance()
			);

			$manager->makeDigest($batch_size,
				NEW_JOBS_REGISTRATION_REQUEST_EMAIL_ALERT_TO,
				Director::absoluteURL('sangria/ViewJobsDetails'));
			return 'OK';
		}
		catch(Exception $ex){
			SS_Log::log($ex,SS_Log::ERR);
			echo $ex->getMessage();
		}
	}
} 