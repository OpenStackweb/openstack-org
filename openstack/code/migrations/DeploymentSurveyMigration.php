<?php

/***
 * Class DeploymentSurveyMigration
 */
final class DeploymentSurveyMigration extends MigrationTask {
	protected $title = "Deployment Survey Migration";

	protected $description = "Migrates all old user surveys answers to new ones.";

	function up()
	{
		echo "Starting  Proc ...<BR>";
		$migration = DataObject::get_one("Migration", "Name='{$this->title}'");
		if (!$migration) {
			$migration = new Migration();
			$migration->Name = $this->title;
			$migration->Description = $this->description;
			$migration->Write();

			//run migration
		$sql = <<< SQL
		update DeploymentSurvey set InformationSources = REPLACE(InformationSources,'ask.openstack.org','Ask OpenStack (ask.openstack.org)')
where InformationSources like '%ask.openstack.org%';
SQL;

			DB::query($sql);

			$sql = <<< SQL
update Deployment set DeploymentStage = REPLACE(DeploymentStage,'Dev/QA','Under development/in testing')
where DeploymentStage like '%Dev/QA%';
SQL;

			DB::query($sql);

			$sql = <<< SQL
update Deployment set OtherWhyNovaNetwork = WhyNovaNetwork
where WhyNovaNetwork is not null;
SQL;

			DB::query($sql);

			$sql = <<< SQL
update Deployment set WhyNovaNetwork = 'Other (please specify)'
where OtherWhyNovaNetwork is not null;
SQL;

			DB::query($sql);
		}
		else{
			echo "Migration Already Ran! <BR>";
		}
		echo "Migration Done <BR>";
	}

	function down()
	{

	}
} 