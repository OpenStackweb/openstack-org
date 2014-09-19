<?php
/**
 * Class EventRegistrationRequestDigestTask
 */
final class EventRegistrationRequestDigestTask extends CliController {

	function process(){

		set_time_limit(0);

		try{

		    $manager = new EventAlertEmailManager (
				new SapphireEventRegistrationRequestRepository,
				new SapphireEventAlertEmailRepository,
				new EventRegistrationRequestFactory,
				SapphireTransactionManager::getInstance()
			);

			$batch_size = 15;
			if(isset($_GET['batch_size'])){
				$batch_size = intval(trim(Convert::raw2sql($_GET['batch_size'])));
			}

			$manager->makeDigest($batch_size,
				NEW_EVENTS_REGISTRATION_REQUEST_EMAIL_ALERT_TO,
				Director::absoluteURL('sangria/ViewEventDetails'));

			return 'OK';
		}
		catch(Exception $ex){
			SS_Log::log($ex,SS_Log::ERR);
			echo $ex->getMessage();
		}
	}
} 