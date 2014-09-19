<?php

/**
 * Class ElectionMigrationTask
 */
final class ElectionMigrationTask extends MigrationTask {

	protected $title = "Election Migration";

	protected $description = "Adds current elections to elections temporal table";

	function up(){
		echo "Starting Migration Proc ...<BR>";
		//check if migration already had ran ...
		$migration = DataObject::get_one("Migration", "Name='{$this->title}'");
		if (!$migration) {

			$g =  new Group;
			$g->setTitle('Community Members');
			$g->setDescription('Community Members');
			$g->setSlug(IFoundationMember::CommunityMemberGroupSlug);
			$g->write();
			$migration = new Migration();
			$migration->Name = $this->title;
			$migration->Description = $this->description;
			$migration->Write();
		}
		echo "Ending  Migration Proc ...<BR>";
	}
}