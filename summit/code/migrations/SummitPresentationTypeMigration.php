<?php

class SummitPresentationTypeMigration extends AbstractDBMigrationTask
{

    protected $title = "Summit Presentation Event Type migration";


    protected $description = "Changes the summit event types to the new type PresentationType for summit barcelona";


    function doUp()
    {
        global $database;
        DB::query("UPDATE SummitEventType SET ClassName = 'PresentationType' WHERE SummitID = 7 AND Type IN ('Presentation','Keynotes','Panel')");
        $res = DB::query("SELECT * FROM SummitEventType WHERE SummitID = 7 AND ClassName = 'PresentationType';");
        foreach($res as $event_type) {
            $min_speakers = 1;
            $max_speakers = 3;
            $min_moderators = $max_moderators = 0;
            switch ($event_type['Type']) {
                case 'Panel':
                    $max_moderators = 1;
            }

            $id = $event_type['ID'];
            DB::query("INSERT INTO PresentationType (ID,MaxSpeakers,MinSpeakers,MaxModerators,MinModerators) VALUES ($id,$max_speakers,$min_speakers,$max_moderators,$min_moderators)");

        }
    }

    function doDown()
    {

    }
}