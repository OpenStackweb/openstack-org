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

use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

final class DBMigrateTask extends MigrationTask
{
    protected $title = "DB migration task";

    protected $description = "DB migration task";


    function up()
    {
        echo "Starting DB Migration Proc ...".PHP_EOL;
        // update list on migrations/migrations.yml
        try {
            $path = Director::baseFolder(). '/migrations/migrations.yml';
            echo "reading migration list from ".$path.' ...'.PHP_EOL;;
            $yaml = Yaml::parse(file_get_contents($path));
            if(!is_null($yaml) && isset($yaml['migrations']) && count($yaml['migrations']))
            {
                echo sprintf("found %s migrations to run ...", count($yaml['migrations'])).PHP_EOL;

                foreach($yaml['migrations'] as $migration_class){
                    $m = new $migration_class;
                    if($m instanceof MigrationTask)
                    {
                        echo "running migration ".$migration_class.PHP_EOL;
                        $m->up();
                    }
                }
            }
            echo "Ending DB Migration Proc ...".PHP_EOL;
        }
        catch (ParseException $e) {
            echo printf("Unable to parse the YAML string: %s", $e->getMessage());
        }
    }

}