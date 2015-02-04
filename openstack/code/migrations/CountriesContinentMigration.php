<?php
/**
 * Copyright 2014 Openstack Foundation
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

class CountriesContinentMigration extends MigrationTask {

    protected $title = "Creates Countries/Continent Tables";

    protected $description = "Creates Countries/Continent Tables";

    function up()
    {
        echo "Starting Migration Proc ...<BR>";
        $migration = Migration::get()->filter('Name',$this->title)->first();
        if (!$migration) {
            $migration = new Migration();
            $migration->Name = $this->title;
            $migration->Description = $this->description;
            $migration->Write();
            DB::query("DROP TABLE IF EXISTS `Continent_Countries`;");
            DB::query("CREATE TABLE `Continent_Countries` (
  `ContinentID` int(11) NOT NULL,
  `CountryCode` varchar(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
            DB::query("LOCK TABLES `Continent_Countries` WRITE;");
            DB::query("INSERT INTO `Continent_Countries` VALUES (3,'AD'),(5,'AE'),(5,'AF'),(1,'AI'),(3,'AL'),(5,'AM'),(1,'AN'),(4,'AO'),(7,'AQ'),(2,'AR'),(1,'AS'),(3,'AT'),(6,'AU'),(3,'AX'),(5,'AZ'),(1,'BB'),(3,'BA'),(5,'BD'),(3,'BE'),(4,'BF'),(3,'BG'),(5,'BH'),(2,'BO'),(2,'BR'),(5,'BT'),(4,'BW'),(4,'ZW'),(4,'ZM'),(4,'ZA'),(5,'VN'),(2,'VE'),(2,'UY'),(1,'US'),(1,'UM'),(4,'UG'),(3,'UA'),(4,'TZ'),(5,'TW'),(1,'TT'),(3,'TR'),(5,'TR'),(4,'TN'),(5,'TH'),(7,'TF'),(1,'TC'),(5,'SY'),(1,'SV'),(4,'SN'),(3,'SK'),(3,'SI'),(5,'SG'),(3,'SE'),(4,'SD'),(8,'SB'),(5,'SA'),(4,'RW'),(3,'RU'),(3,'RS'),(3,'RO'),(5,'QA'),(2,'PY'),(3,'PT'),(1,'PR'),(3,'PL'),(5,'PK'),(5,'PH'),(8,'PG'),(8,'PF'),(2,'PE'),(1,'PA'),(8,'NZ'),(5,'NP'),(3,'NO'),(3,'NL'),(4,'NG'),(4,'NE'),(4,'NA'),(5,'MY'),(1,'MX'),(4,'MU'),(3,'MT'),(5,'MO'),(5,'MN'),(5,'MM'),(3,'MK'),(3,'ME'),(3,'MD'),(3,'MC'),(4,'MA'),(4,'LY'),(3,'LV'),(3,'LU'),(3,'LT'),(5,'LK'),(1,'LC'),(5,'LB'),(5,'KZ'),(1,'KY'),(5,'KW'),(5,'KR'),(5,'KP'),(5,'KH'),(4,'KE'),(5,'JP'),(5,'JO'),(1,'JM'),(3,'IT'),(3,'IS'),(5,'IR'),(4,'IO'),(5,'IN'),(5,'IL'),(3,'IE'),(5,'ID'),(3,'HU'),(3,'HR'),(1,'HN'),(5,'HK'),(1,'GT'),(3,'GR'),(1,'GP'),(3,'BY'),(1,'CA'),(4,'CM'),(1,'CR'),(3,'CY'),(3,'DK'),(2,'EC'),(3,'ES'),(3,'FR'),(3,'GE'),(3,'CH'),(4,'CI'),(2,'CL'),(5,'CN'),(2,'CO'),(1,'CU'),(3,'CZ'),(3,'DE'),(3,'FI'),(3,'FX'),(4,'GH'),(8,'CX'),(1,'DO'),(4,'DZ'),(3,'EE'),(4,'EG'),(3,'GB');");
            DB::query("UNLOCK TABLES;");
            DB::query("DROP TABLE IF EXISTS `Continent`;");
            DB::query("CREATE TABLE `Continent` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(32) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;");
            DB::query("LOCK TABLES `Continent` WRITE;");
            DB::query("INSERT INTO `Continent` VALUES (1,'North America'),(2,'South America'),(3,'Europe'),(4,'Africa'),(5,'Asia'),(6,'Australia'),(7,'Antarctica'),(8,'Oceania');");
            DB::query("UNLOCK TABLES;");
        }
        else{
            echo "Migration Already Ran! <BR>";
        }
        echo "Migration Done <BR>";
    }

    function down()
    {

    }

}