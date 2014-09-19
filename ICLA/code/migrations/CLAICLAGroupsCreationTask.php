<?php

/**
 * Class CLAICLAGroupsCreationTask
 */
final class CLAICLAGroupsCreationTask extends MigrationTask {

	protected $title = "CCLA/ICLA Migration";

	protected $description = "Creates CLA/ICLA Security Groups";

	function up(){
		echo "Starting Migration Proc ...<BR>";
		//check if migration already had ran ...
		$migration = Migration::get()->filter('Name',$this->title)->first();
		if (!$migration) {
			$g = new Group();
			$g->setTitle('CCLA Admin');
			$g->setDescription('Company CCLA Admin');
			$g->setSlug(ICLAMemberDecorator::CCLAGroupSlug);
			$g->write();

			Permission::grant($g->getIdentifier(),ICLAMemberDecorator::CCLAPermissionSlug);

			$migration = new Migration();
			$migration->Name = $this->title;
			$migration->Description = $this->description;
			$migration->Write();
		}
		echo "Ending  Migration Proc ...<BR>";
	}

	function down()	{

	}
} 