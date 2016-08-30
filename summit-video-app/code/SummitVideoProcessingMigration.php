<?php

/**
 * Class SummitVideoProcessingMigration
 */
class SummitVideoProcessingMigration extends AbstractDBMigrationTask
{

    /**
     * @var string
     */
    protected $title = 'Summit Video Processing Migration';

    /**
     * @var string
     */
    protected $description = 'Sets all videos to "processed"';


    /**
     * Run the migration
     */
    public function doUp()
    {
        global $database;

        if (DBSchema::existsColumn($database, 'PresentationVideo', 'Processed')) {
            DB::query("UPDATE PresentationVideo SET Processed = 1");
        }
    }
}
