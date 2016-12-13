<?php

/**
 * Created by PhpStorm.
 * User: smarcet
 * Date: 3/5/16
 * Time: 4:46 PM
 */
class ClearOlderIngestTaskMigration extends AbstractDBMigrationTask
{
    protected $title = "ClearOlderIngestTaskMigration";

    protected $description = "ClearOlderIngestTaskMigration";

    function doUp()
    {
        global $database;

        $task = BatchTask::get()->filter('Name', SpeakerEmailAnnouncementSenderManager::TaskName)->first();
        if(!is_null($task)) $task->delete();
    }

    function doDown()
    {
        // TODO: Implement doDown() method.
    }
}