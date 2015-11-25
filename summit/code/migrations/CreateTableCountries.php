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
final class CreateTableCountries extends AbstractDBMigrationTask
{
    protected $title = "Create table countries";

    protected $description = "Create a table that links country codes with country names for search purposes";

    function doUp()
    {
        $CountryCodes = CountryCodes::$iso_3166_countryCodes;

        $sql_values = '';
        foreach ($CountryCodes as $code=>$country) {
            $sql_values .= "('".str_replace("'","''",$country)."','$code'),";
        }
        $sql_values = substr($sql_values,0,-1);


        DB::query("DROP TABLE IF EXISTS `Countries`;");
        DB::query("CREATE TABLE `Countries` (
  `Name` varchar(254) NOT NULL,
  `Code` varchar(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

        DB::query("LOCK TABLES `Countries` WRITE;");
        DB::query("INSERT INTO `Countries` VALUES $sql_values;");
        DB::query("UNLOCK TABLES;");

    }

    function doDown()
    {

    }
}