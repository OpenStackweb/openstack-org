<?php

/**
 * Class NewsMigrationTask
 */
final class NewsMigrationTask extends MigrationTask {

	protected $title = "News Migration";

	protected $description = "Adds current news to news table";

	function up(){
		echo "Starting Migration Proc ...<BR>";
		//check if migration already had ran ...
		$migration = DataObject::get_one("Migration", "Name='{$this->title}'");
		if (!$migration) {

			$g =  new Group;
			$g->setTitle('News Manager');
			$g->setDescription('News Manager');
			$g->setSlug(INewsManager::NewsManagerGroupSlug);
			$g->write();
			$migration = new Migration();
			$migration->Name = $this->title;
			$migration->Description = $this->description;
			$migration->Write();
		}
		echo "Ending  Migration Proc ...<BR>";
	}
}