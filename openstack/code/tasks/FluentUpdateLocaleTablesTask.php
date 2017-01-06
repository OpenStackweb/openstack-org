<?php

/**
 * Copyright 2016 OpenStack Foundation
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
class FluentUpdateLocaleTablesTask extends CronTask
{

    protected $title = "Deprecated Locate Cleaner (FLUENT)";

    protected $description = "Remove unused locales from Page Tables (All SiteTree subclasses)";

    /**
     * Implement this method in the task subclass to
     * execute via the TaskRunner
     */
    public function run()
    {
        try {
            $init_time = time();
            global $database;
            // todo : get a better way to get the invalid ones ...
            $invalid_locales = [
                'en_NZ',
                'fr_FR',
                'es_ES',
                'de_DE',
                'th_TH',
                'ja_JP',
                'ko_KR'
            ];
            // get all subclasses for site tree
            $site_tree_subclasses = ClassInfo::subclassesFor('SiteTree');
            foreach ($site_tree_subclasses as $subclass) {
                $base_fields = DataObject::custom_database_fields($subclass);
                //FluentExtension::translated_fields_for($subclass);
                foreach ($base_fields as $field => $type) {
                    foreach ($invalid_locales as $invalid_locale) {

                        $invalid_column = Fluent::db_field_for_locale($field, $invalid_locale);
                        $table_versions = [
                            $subclass,
                            $subclass . '_Live',
                            $subclass . '_versions',
                        ];
                        foreach ($table_versions as $table) {
                            if (!DBSchema::existsTable($database, $table)) continue;
                            if (DBSchema::existsColumn($database, $table, $invalid_column)) {
                                DBSchema::dropColumn($database, $table, $invalid_column);
                            }
                        }
                    }
                }
            }
            $finish_time = time() - $init_time;
            echo 'time elapsed : ' . $finish_time . ' seconds.' . PHP_EOL;
        }
        catch(Exception $ex)
        {
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
            throw $ex;
        }
    }
}