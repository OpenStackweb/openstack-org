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
class AbstractDBMigrationTask extends MigrationTask
{
    protected $title;

    protected $description;

    function up()
    {
        echo sprintf("starting migration # %s ...", $this->title).PHP_EOL;
        //check if migration already had ran ...
        $migration = Migration::get()->filter('Name', $this->title)->first();

        if (!$migration) {

            set_time_limit(0);

            $this->doUp();
            $migration = new Migration();
            $migration->Name = $this->title;
            $migration->Description = $this->description;
            $migration->Write();
            echo sprintf("ending migration # %s ...", $this->title).PHP_EOL;
        }
        else
        {
            echo sprintf("migration # %s already ran !...", $this->title).PHP_EOL;
        }
    }

    function down()
    {
        $this->doDown();
    }

    public function doUp(){}

    public function doDown(){}

}