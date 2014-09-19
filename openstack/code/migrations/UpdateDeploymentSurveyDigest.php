<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Patricio T
 * Date: 10/29/13
 */

class UpdateDeploymentSurveyDigest extends MigrationTask
{

	protected $title = "Update Deployment Survey Digest Field";

	protected $description = "Set SendDigest = 1 for all the old survey deployments";

	function up()
	{
		echo "Starting  Proc ...<BR>";
		$migration = Migration::get()->filter('Name',$this->title)->first();
		if (!$migration) {
			$migration = new Migration();
			$migration->Name = $this->title;
			$migration->Description = $this->description;
			$migration->Write();
		   
			//run migration
			$surveys = DeploymentSurvey::get();

			foreach($surveys as $survey){
				$survey->SendDigest = 1;
				$survey->write();
			}
			
			
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