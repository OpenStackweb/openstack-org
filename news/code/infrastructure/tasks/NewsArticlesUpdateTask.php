<?php

/**
 * Class ExpirationRemovalTask
 */
final class NewsArticlesUpdateTask extends ScheduledTask {

	function process(){

		set_time_limit(0);

		try{
            $manager = new NewsRequestManager(
                new SapphireNewsRepository,
                new SapphireSubmitterRepository,
                new NewsFactory,
                new NewsValidationFactory,
                new SapphireFileUploadService(),
                SapphireTransactionManager::getInstance()
            );

            $manager->removeExpired();
            $manager->activateArticles();

			return 'OK';
		}
		catch(Exception $ex){
			SS_Log::log($ex,SS_Log::ERR);
			echo $ex->getMessage();
		}
	}
} 