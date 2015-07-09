<?php
/**
 * Created by PhpStorm.
 * User: claudio
 * Date: 7/9/15
 * Time: 5:25 PM
 */

final class Live2StageEventMigrationTask extends MigrationTask {

    protected $title = "EventPage_Live 2 EventPage records migration";

    protected $description = "EventPage_Live 2 EventPage records migration";

    function up(){
        echo "Starting Migration Proc ...<BR>";
        //check if migration already had ran ...
        $migration = DataObject::get_one("Migration", "Name='{$this->title}'");
        if (!$migration) {

            DB::getConn()->transactionStart();
            try{

                DB::query("DELETE FROM EventPage");
                DB::query("
INSERT INTO EventPage
		(ID,
		EventStartDate,
		EventEndDate,
		EventLink,
		EventLinkLabel,
		EventLocation,
		EventSponsor,
		EventSponsorLogoUrl,
		IsSummit,
		EventCategory,
		ExternalSourceId,
		ClassName,
		Created,
		LastEdited,
		Title)
SELECT 	EventPage_Live.ID,
		EventStartDate,
		EventEndDate,
		EventLink,
		EventLinkLabel,
		EventLocation,
		EventSponsor,
		EventSponsorLogoUrl,
		IsSummit,
		EventCategory,
		ExternalSourceId,
		ClassName,
		Created,
		LastEdited,
		Title
FROM  	EventPage_Live
INNER JOIN Page_Live on Page_Live.Id = EventPage_Live.Id
INNER JOIN SiteTree_Live on SiteTree_Live.Id = EventPage_Live.Id
                ");
                DB::query("DROP TABLE EventPage_Live");
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