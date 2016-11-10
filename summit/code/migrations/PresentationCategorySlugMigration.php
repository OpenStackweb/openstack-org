<?php

class PresentationCategorySlugMigration extends AbstractDBMigrationTask
{

    protected $title = "Presentation Category Slug migration";


    protected $description = "set the slug for all presentation categories";


    function doUp()
    {
        global $database;
        $categories = PresentationCategory::get()->toArray();

        foreach ($categories as $cat) {
            $clean_title = preg_replace ("/[^a-zA-Z0-9 ]/", "", $cat->Title);
            $slug = preg_replace('/\s+/', '-', strtolower($clean_title));

            DB::query("UPDATE PresentationCategory SET Slug = '{$slug}' WHERE ID = ".$cat->ID);
        }

    }

    function doDown()
    {

    }
}