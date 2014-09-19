<?php

/**
 * Class UpdateLastCommittedDateTask
 */
final class UpdateLastCommittedDateTask extends CliController {

	function process(){

		set_time_limit(0);

		$manager = new ICLAManager (
			new GerritAPI(GERRIT_BASE_URL, GERRIT_USER, GERRIT_PASSWORD),
			new SapphireBatchTaskRepository,
			new SapphireCLAMemberRepository,
			new BatchTaskFactory,
			SapphireTransactionManager::getInstance()
		);

		$members_updated = $manager->updateLastCommittedDate(PULL_LAST_COMMITTED_DATA_FROM_GERRIT_BATCH_SIZE);

		echo $members_updated;
	}
}