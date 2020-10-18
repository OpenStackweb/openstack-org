<?php
/**
 * Copyright 2020 Open Infrastructure Foundation
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

/***
 * Class MakeMarketplaceModelCompatibleWithDoctrineORMMigration
 * this migration is aim to insert all missing FK on the child tables
 * in order to make possible the inner join performed by Doctrine ORM
 */
final class MakeMarketplaceModelCompatibleWithDoctrineORMMigration extends AbstractDBMigrationTask {

    protected $title = "MakeMarketplaceModelCompatibleWithDoctrineORMMigration";

    protected $description = "MakeMarketplaceModelCompatibleWithDoctrineORMMigration";

    function doUp(){

        global $database;

        if(DBSchema::existsTable($database, "RegionalSupportedCompanyService"))
            DB::query("
            INSERT IGNORE INTO RegionalSupportedCompanyService (ID) SELECT ID from CompanyService where ClassName IN ('Appliance', 'Distribution', 'PrivateCloudService', 'PublicCloudService', 'RemoteCloudService', 'Consultant');
            ");

        if(DBSchema::existsTable($database, "OpenStackImplementation"))
        DB::query("
            INSERT IGNORE INTO OpenStackImplementation (ID) SELECT ID from CompanyService where ClassName 
    IN ('PrivateCloudService', 'RemoteCloudService', 'PublicCloudService', 'Appliance', 'Distribution');");

        if(DBSchema::existsTable($database, "Consultant"))
            DB::query("
            INSERT IGNORE INTO Consultant (ID) SELECT ID from CompanyService where ClassName IN ('Consultant');
            ");

        if(DBSchema::existsTable($database, "Appliance"))
            DB::query("
            INSERT IGNORE INTO Appliance (ID) SELECT ID from CompanyService where ClassName IN ('Appliance');
            ");

        if(DBSchema::existsTable($database, "Distribution"))
            DB::query("
            INSERT IGNORE INTO Distribution (ID) SELECT ID from CompanyService where ClassName IN ('Distribution');
            ");

        if(DBSchema::existsTable($database, "CloudService"))
            DB::query("
            INSERT IGNORE INTO CloudService (ID) SELECT ID from CompanyService where ClassName IN ('PrivateCloudService', 'PublicCloudService');
            ");

        if(DBSchema::existsTable($database, "PrivateCloudService"))
            DB::query("
            INSERT IGNORE INTO PrivateCloudService (ID) SELECT ID from CompanyService where ClassName IN ('PrivateCloudService');
            ");

        if(DBSchema::existsTable($database, "PublicCloudService"))
            DB::query("
            INSERT IGNORE INTO PublicCloudService (ID) SELECT ID from CompanyService where ClassName IN ('PublicCloudService');
            ");

        if(DBSchema::existsTable($database, "PublicCloudService"))
            DB::query("
            INSERT IGNORE INTO PublicCloudService (ID) SELECT ID from CompanyService where ClassName IN ('PublicCloudService');
            ");

    }
}