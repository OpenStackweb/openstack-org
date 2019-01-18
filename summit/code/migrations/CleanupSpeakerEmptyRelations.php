<?php

class CleanupSpeakerEmptyRelations extends AbstractDBMigrationTask
{

    protected $title = "CleanupSpeakerEmptyRelations";


    protected $description = "clean up orphaned relations for speakers";


    function doUp()
    {
        global $database;
        DB::query("DELETE FROM SpeakerPresentationLink WHERE SpeakerID = 0");
        DB::query("DELETE FROM SpeakerTravelPreference WHERE SpeakerID = 0");
        DB::query("DELETE FROM SpeakerExpertise WHERE SpeakerID = 0");
    }

    function doDown()
    {

    }
}