<?php

class SummitRandomVotingMigrationTask extends BuildTask {

	public function run ($request) {
		DB::query("DROP TABLE IF EXISTS PresentationPriority");
	}
}