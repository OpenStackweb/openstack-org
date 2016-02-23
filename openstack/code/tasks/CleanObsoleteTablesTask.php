<?php

/**
 * Copyright 2015 OpenStack Foundation
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
final class CleanObsoleteTablesTask extends CronTask {


    /**
     * @return void
     */
    public function run()
    {
        global $database;

        $res = DB::query("SELECT TABLE_NAME FROM information_schema.TABLES
    WHERE TABLE_SCHEMA = '{$database}' AND TABLE_NAME LIKE '%_obsolete_%';");

        foreach($res as $table_name)
        {
            echo "deleting table {$table_name['TABLE_NAME']} ...";
            DB::query("DROP TABLE {$table_name['TABLE_NAME']};");
        }
    }
}