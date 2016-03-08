<?php

/**
 * Created by PhpStorm.
 * User: smarcet
 * Date: 3/8/16
 * Time: 10:11 AM
 */
class RemoveNullMembersMigration extends AbstractDBMigrationTask
{
    protected $title = "RemoveNullMembersMigration";

    protected $description = "RemoveNullMembersMigration";

    function doUp()
    {
        DB::query("DELETE FROM Member WHERE Email IS NULL AND Password IS NULL AND FirstName IS NULL AND SurName IS NULL;");
    }

    function doDown()
    {
        // TODO: Implement doDown() method.
    }
}