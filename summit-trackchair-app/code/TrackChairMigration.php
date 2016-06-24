<?php

class TrackChairMigration extends AbstractDBMigrationTask
{

	public function doUp()
	{
        global $database;

        if (DBSchema::existsColumn($database, 'SummitCategoryChange', 'Done')) {
            DB::query("UPDATE SummitCategoryChange SET Status = 1 WHERE Done=1");
            DB::query("UPDATE SummitCategoryChange SET Status = 0 WHERE Done=0");
        }

        DB::query("UPDATE SummitSelectedPresentation SET Collection = 'selected'");

        if (DBSchema::existsColumn($database, 'SummitPresentationComment', 'IsCategoryChangeSuggestion')) {
            DB::query("UPDATE SummitPresentationComment SET IsActivity = 1 WHERE IsCategoryChangeSuggestion = 1");
        }
	}
}
