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
/**
 * Class RemoveSiteTreeForEventPageMigrationTask
 */
class RemoveSiteTreeForEventPageMigrationTask extends MigrationTask {

    protected $title = "Remove cms records for EventPage";

    protected $description = "Remove records from table SiteTree, SiteTree_Live, Page and Page_Live related to EventPage";

    function up(){
        echo "Starting Migration Proc ...<BR>";
        //check if migration already had ran ...
        $migration = DataObject::get_one("Migration", "Name='{$this->title}'");
        if (!$migration) {

            DB::getConn()->transactionStart();
            try{
                DB::query("delete from Page where Id in (select Id from SiteTree where ClassName = 'EventPage')");
                DB::query("delete from SiteTree where ClassName = 'EventPage'");
                DB::query("delete from Page_Live where Id in (select Id from SiteTree_Live where ClassName = 'EventPage')");
                DB::query("delete from SiteTree_Live where ClassName = 'EventPage'");
                DB::getConn()->transactionEnd();
            }catch(Exception $e){
                DB::getConn()->transactionRollback();
            }

            $migration = new Migration();
            $migration->Name = $this->title;
            $migration->Description = $this->description;
            $migration->Write();
        }
        echo "Ending  Migration Proc ...<BR>";
    }
}