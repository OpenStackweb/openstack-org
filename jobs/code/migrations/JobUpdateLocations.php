<?php

final class JobUpdateLocations extends MigrationTask {

	protected $title = "Jobs Updates Locations Migration";

	protected $description = "Update Locations for current Jobs";

	function up(){
		echo "Starting Migration Proc ...<BR>";
		//check if migration already had ran ...
		$migration = Migration::get()->filter('Name',$this->title)->first();
		if (!$migration) {

			$requests = JobRegistrationRequest::get();

			foreach($requests as $request){
				if(!empty($request->City)){
					//create locations
					$location            = new JobLocation();
					$location->City      = $request->City;
					$location->State     = $request->State;
					$location->Country   = $request->Country;
					$location->RequestID = $request->ID;
					$location->Write();
					$request->LocationType = 'Various';
					$request->Write();
				}
			}

			$migration = new Migration();
			$migration->Name = $this->title;
			$migration->Description = $this->description;
			$migration->Write();
		}
		echo "Ending  Migration Proc ...<BR>";
	}

	function down()	{

	}

} 