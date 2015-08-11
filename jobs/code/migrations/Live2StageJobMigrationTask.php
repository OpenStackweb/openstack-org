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
 * Class Live2StageEventMigrationTask
 */

final class Live2StageJobMigrationTask extends MigrationTask {

    protected $title = "JobPage_Live 2 JobPage records migration";

    protected $description = "JobPage_Live 2 JobPage records migration";

    function up(){
        echo "Starting Migration Proc ...<BR>";
        //check if migration already had ran ...
        $migration = DataObject::get_one("Migration", "Name='{$this->title}'");
        if (!$migration) {

            DB::getConn()->transactionStart();
            try{

                // migrate records from Live to Stage table
                DB::query("DELETE FROM JobPage");
                DB::query("
INSERT INTO JobPage
		(ID,
		JobPostedDate,
		JobCompany,
		JobMoreInfoLink,
		JobLocation,
		FoundationJob,
		ExpirationDate,
		Active,
		JobInstructions2Apply,
		LocationType,
		Title,
		Content)
SELECT 	JobPage_Live.ID,
		JobPostedDate,
		JobCompany,
		JobMoreInfoLink,
		JobLocation,
		FoundationJob,
		ExpirationDate,
		Active,
		JobInstructions2Apply,
		LocationType,
		Title,
		Content
FROM  	JobPage_Live
INNER JOIN Page_Live on Page_Live.Id = JobPage_Live.Id
INNER JOIN SiteTree_Live on SiteTree_Live.Id = JobPage_Live.Id");

                DB::query("DROP TABLE JobPage_Live");
                DB::query("DROP TABLE JobPage_versions");

                // delete orphan records
                DB::query("delete from Page where Id in (select Id from SiteTree where ClassName = 'JobPage')");
                DB::query("delete from SiteTree where ClassName = 'JobPage'");
                DB::query("delete from Page_Live where Id in (select Id from SiteTree_Live where ClassName = 'JobPage')");
                DB::query("delete from SiteTree_Live where ClassName = 'JobPage'");
                DB::query("delete from Page_versions where Id in (select Id from SiteTree_versions where ClassName = 'JobPage')");
                DB::query("delete from SiteTree_versions where ClassName = 'JobPage'");                DB::getConn()->transactionEnd();
            }catch(Exception $e){
                DB::getConn()->transactionRollback();
                return;
            }

            $migration = new Migration();
            $migration->Name = $this->title;
            $migration->Description = $this->description;
            $migration->Write();
        }
        echo "Ending  Migration Proc ...<BR>";
    }
}