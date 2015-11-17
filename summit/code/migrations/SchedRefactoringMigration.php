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
final class SchedRefactoringMigration extends AbstractDBMigrationTask
{
    protected $title = "Sched Refactoring #1";

    protected $description = "Sched Refactoring #1";

    function doUp()
    {

            $SQL = <<<SQL
 update Presentation set SummitID = 5;
SQL;

            DB::query($SQL);

            DB::query('DELETE FROM SummitEvent;');


            $SQL = <<<SQL

INSERT INTO SummitEvent
(ID, Created, LastEdited, ClassName, Title, Description, SummitID)
SELECT ID, Created, LastEdited, ClassName, Title, Description, SummitID FROM Presentation;
SQL;

            DB::query($SQL);

            $SQL = <<<SQL
ALTER TABLE Presentation
  DROP COLUMN Title,
  DROP COLUMN SummitID,
  DROP COLUMN Description;
SQL;

            DB::query($SQL);

            $SQL = <<<SQL
INSERT INTO SummitAbstractLocation
(ID, ClassName, Created, LastEdited, `Name`, Description, `Order`, SummitID, LocationType)
SELECT ID, 'SummitVenue', Created, LastEdited, `Name` , Description, `Order`,
CASE SummitLocationPageID
WHEN 2345 THEN 4
WHEN 2579 THEN 5
ELSE NULL
END AS SummitID,
'Internal'
from SummitLocation where Type ='Venue';
SQL;

            DB::query($SQL);

            $SQL = <<<SQL
INSERT INTO SummitGeoLocatedLocation
(ID, Address1, WebSiteUrl, Lat, Lng, DisplayOnSite)
SELECT ID,Address, Website, Latitude, Longitude, DisplayOnSite
from SummitLocation where Type ='Venue';
SQL;

            DB::query($SQL);

            $SQL = <<<SQL
INSERT INTO SummitAbstractLocation
(ID, ClassName, Created, LastEdited, `Name`, Description, `Order`, SummitID, LocationType)
SELECT ID, 'SummitHotel', Created, LastEdited, `Name` , Description, `Order`,
CASE SummitLocationPageID
WHEN 2345 THEN 4
WHEN 2579 THEN 5
ELSE 0
END AS SummitID,
'External'
from SummitLocation where Type ='Hotel';
SQL;

            DB::query($SQL);

            $SQL = <<<SQL
INSERT INTO SummitGeoLocatedLocation
(ID, Address1, WebSiteUrl, Lat, Lng, DisplayOnSite)
SELECT ID,Address, Website, Latitude, Longitude, DisplayOnSite
from SummitLocation where Type ='Hotel';
SQL;

            DB::query($SQL);

            $SQL = <<<SQL
INSERT INTO SummitHotel
(ID, BookingLink, SoldOut)
SELECT ID,BookingLink, IsSoldOut
from SummitLocation where Type ='Hotel';
SQL;

            DB::query($SQL);

            $SQL = <<<SQL
INSERT INTO SummitAbstractLocation
(ID, ClassName, Created, LastEdited, `Name`, Description, `Order`, SummitID, LocationType)
SELECT ID, 'SummitAirport', Created, LastEdited, `Name` , Description, `Order`,
CASE SummitLocationPageID
WHEN 2345 THEN 4
WHEN 2579 THEN 5
ELSE 0
END AS SummitID,
'External'
from SummitLocation where Type ='Airport';
SQL;

            DB::query($SQL);

            $SQL = <<<SQL
INSERT INTO SummitGeoLocatedLocation
(ID, Address1, WebSiteUrl, Lat, Lng, DisplayOnSite)
SELECT ID,Address, Website, Latitude, Longitude, DisplayOnSite
from SummitLocation where Type ='Airport';
SQL;

            DB::query($SQL);

            $SQL = <<<SQL
INSERT INTO
SummitAirport
(ID)
SELECT ID from SummitLocation where Type ='Airport';
SQL;

            DB::query($SQL);

    }

    function doDown()
    {

    }
}