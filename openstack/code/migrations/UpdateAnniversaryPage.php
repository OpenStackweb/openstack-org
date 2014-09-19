<?php

/**
 * Class UpdateAnniversaryPage
 */
final class UpdateAnniversaryPage extends MigrationTask {

	protected $title = "Update Anniversary Page 4bDay";

	protected $description = "Update Anniversary Page 4bDay";

	function up()
	{
		echo "Starting Migration Proc ...<BR>";
		$migration = Migration::get()->filter('Name',$this->title)->first();
		if (!$migration) {


			//run migration
			$query = <<<SQL
update SiteTree_Live
SET ClassName='AnniversaryPage'
where ID = 891;
SQL;

			DB::query($query);

			$migration = new Migration();
			$migration->Name = $this->title;
			$migration->Description = $this->description;
			$migration->Write();

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