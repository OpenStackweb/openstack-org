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

use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;
/**
 * Class CustomDatabaseAdmin
 */
class CustomDatabaseAdmin extends DatabaseAdmin
{
    public function build() {
        parent::build();
        $this->buildMandatoryTables();
    }

    /**
     * SS by default does not create any dataobject that does not
     * has at least one db field or FK field
     * but on Doctrine ORM is mandatory to have tables at least with ID
     * in order to perform proper inner joins to get all nodes of object
     * hierarchy
     */
    private function buildMandatoryTables(){
        global $database;
        try {
            $path = Director::baseFolder(). '/database/_config/mandatory_dataobjects.yml';
            $yaml = Yaml::parse(file_get_contents($path));
            if (!is_null($yaml) && isset($yaml['dataobjects']) && count($yaml['dataobjects'])) {

                foreach ($yaml['dataobjects'] as $index => $dataobject) {

                    $class       = $dataobject['class'];
                    $insert_sql  = $dataobject['insert_sql'];

                        if(class_exists($class)) {
                            $fields         = DataObject::database_fields($class);
                            $has_own_table  = DataObject::has_own_table($class);
                            if(empty($fields) && !DBSchema::existsTable($database, $class) && !$has_own_table){
                                $singleton = singleton($class);
                                echo "* Creating mandatory table {$class} ...".PHP_EOL;
                                // create table only with ID
                                DB::query("CREATE TABLE `{$class}` (
                      `ID` int(11) NOT NULL AUTO_INCREMENT,
                      PRIMARY KEY (`ID`)
                    ) ENGINE=InnoDB  DEFAULT CHARSET=latin1;");

                                // create migration
                                if(!empty($insert_sql)){
                                    DB::query($insert_sql);
                                }
                            }

                        }

                }
            }
        }
        catch (ParseException $e) {
            echo printf("Unable to parse the YAML string: %s", $e->getMessage());
        }
    }
}