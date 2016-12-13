<?php

class SummitCategoryDefaultTagsMigration extends AbstractDBMigrationTask
{

    protected $title = "Summit Category Default Tags Migration";


    protected $description = "copy the default category tags from Barcelona summit to new summits";


    function doUp()
    {
        global $database;

        $new_summits = DB::query("SELECT ID FROM Summit WHERE SummitBeginDate > DATE(NOW())");
        foreach ($new_summits as $new_summit) {
            $summit_id = $new_summit['ID'];

            DB::query("INSERT INTO Summit_CategoryDefaultTags (SummitID,TagID,`Group`)
                       SELECT $summit_id AS SummitID, TagID, `Group` FROM Summit_CategoryDefaultTags WHERE SummitID = 7;");
        }

    }

    function doDown()
    {

    }
}