<?php

class SummitSlugMigration extends AbstractDBMigrationTask
{

    protected $title = "Summit Slug migration";


    protected $description = "set the slug for all summits";


    function doUp()
    {
        global $database;
        DB::query("UPDATE Summit SET Slug = 'hong-kong-2013' WHERE ID = 1");
        DB::query("UPDATE Summit SET Slug = 'paris-2014' WHERE ID = 3");
        DB::query("UPDATE Summit SET Slug = 'vancouver-2015' WHERE ID = 4");
        DB::query("UPDATE Summit SET Slug = 'tokio-2015' WHERE ID = 5");
        DB::query("UPDATE Summit SET Slug = 'austin-2016' WHERE ID = 6");
        DB::query("UPDATE Summit SET Slug = 'barcelona-2016' WHERE ID = 7");
        DB::query("UPDATE Summit SET Slug = 'san-diego-2012' WHERE ID = 19");
        DB::query("UPDATE Summit SET Slug = 'portland-2013' WHERE ID = 20");
        DB::query("UPDATE Summit SET Slug = 'atlanta-2013' WHERE ID = 21");
        DB::query("UPDATE Summit SET Slug = 'boston-2017' WHERE ID = 22");

    }

    function doDown()
    {

    }
}