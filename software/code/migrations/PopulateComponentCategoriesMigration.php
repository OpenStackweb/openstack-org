<?php

/**
 * Copyright 2017 OpenStack Foundation
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 **/

class PopulateComponentCategoriesMigration extends AbstractDBMigrationTask
{
    protected $title = "PopulateComponentCategoriesMigration";

    protected $description = "PopulateComponentCategoriesMigration";

    function doUp()
    {
        global $database;

        $categories = array(
            "Compute",
            "Storage, Backup & Recovery",
            "Networking & Content Delivery",
            "Data & Analytics",
            "Security, Identity & Compliance",
            "Management Tools",
            "Deployment Tools",
            "Application Services",
            "None"
        );

        foreach ($categories as $order => $category) {
            if (!$new_category = OpenStackComponentCategory::get()->filter('Name', $category)->first()) {
                $new_category = new OpenStackComponentCategory();
                $new_category->Name = $category;
                $new_category->Order = $order;
            }

            $component_ids = OpenStackComponent::get()->filter('Use', $category)->column('ID');
            if ($component_ids) {
                $new_category->OpenStackComponents()->setByIDList($component_ids);
            }

            $new_category->write();
        }

        $SQL = <<<SQL
        ALTER TABLE OpenStackComponent DROP COLUMN `Use`
SQL;
        DB::query($SQL);
    }

    function doDown()
    {

    }
}