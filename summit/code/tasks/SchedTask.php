<?php

/**
 * This task syncs the local database to the remote Sched database via the API.
 * Sched is assumed to be authoritative, and therefore, no changes are pushed
 * from local to remote. Optionally, the task will destroy records locally
 * to sync with the remote.
 *
 * @package  presentations
 * @author  Aaron Carlino <unclecheese@leftandmain.com>
 * 
 */
class SchedTask extends CliController {

	/**
	 * @var string $title Shown in the overview on the {@link TaskRunner}
	 * HTML or CLI interface. Should be short and concise, no HTML allowed.
	 */
	protected $title = "Sched Import Task";
	
	/**
	 * @var string $description Describe the implications the task has,
	 * and the changes it makes. Accepts HTML formatting.
	 */
	protected $description = 'Import sessions from Sched into SchedPresentation records';
	

	/**
	 * Run the task
	 * @param  SS_HTTPRequest $request 	 
	 */
	public function index() {		
		$sync = $this->request->requestVar('sync');
		$sessions = SchedAPI::create()->getSessions();		
		$i = 0;
		if($sessions) {
			$this->writeOut("**** Updating from remote ****");
			$this->loadSessions($sessions);

			if($sync) {
				$this->writeOut("**** Syncing to remote ****");
				$this->deleteSessions($sessions);
			}
		}
		else {
			$this->writeOut("No presentations found.");
		}
	}


	/**
	 * Loads the sessions from remote. Update only. Remote is authoritative
	 * @param  array $sessions The session data from the API	 
	 */
	protected function loadSessions($sessions) {
		$i = 0;
		foreach($sessions as $sessionData) {
			$record = SchedPresentation::get_by_sched_id($sessionData['id']);
			
			if(!$record) {
				$this->writeOut("No presentation with Sched ID " . $sessionData['id'] . " exists. Creating.");
				$record = SchedPresentation::create();
			}
			else {
				$this->writeOut("Found existing presentation with Sched ID " . $sessionData['id']);
			}

			$presentation = $this->loadIntoPresentation($record, $sessionData);
			$presentation->write();
			$i++;

		}

		$this->writeOut("Loaded $i presentations.");
	}


	/**
	 * Syncs the local sessions to remote. Deletes any local sessions that no longer exist on remote
	 * @param  array $sessions The session data from the API	 
	 */
	protected function deleteSessions($sessions) {
		$i = 0;
		foreach(SchedPresentation::get() as $existing) {
			$found = false;
			foreach($sessions as $sessionData) {
				if($sessionData['id'] == $existing->SchedID) {
					$found = true;
					break;
				}
			}

			if(!$found) {
				$this->writeOut("Presentation {$existing->Title} no longer exists on remote. Deleting.");
				$existing->delete();
				$i++;
			}
		}

		$this->writeOut("$i presentations deleted.");
	}


	/**
	 * Loads array data from the API into a SchedPresentation object
	 * @param  SchedPresentation $presentation 
	 * @param  array             $data         The session data
	 * @return SchedPresentation
	 */
	protected function loadIntoPresentation(SchedPresentation $presentation, $data = array ()) {
		$presentation->Title = $data['name'];
		$presentation->Description = @$data['description'];
		$presentation->EventEnd = $data['event_end'];
		$presentation->EventStart = $data['event_start'];
		$presentation->EventKey = $data['event_key'];
		$presentation->EventType = @$data['event_type'];
		$presentation->Goers = $data['goers'];
		$presentation->SchedID = $data['id'];
		$presentation->InviteOnly = ($data['invite_only'] == "Y");
		$presentation->Seats = $data['seats'];
		$presentation->Speakers = @$data['speakers'];
		$presentation->Venue = $data['venue'];
		$presentation->VenueID = $data['venue_id'];
		$presentation->DisplayOnSite = ($data['active'] == "Y");
		$presentation->SummitID = Summit::get_active()->ID;

		return $presentation;
	}


	/**
	 * A helper method to write output. Use rich text if in a browser
	 * @param  string $msg The text to write	 
	 */
	protected function writeOut($msg) {
		if(Director::is_cli()) {
			fwrite(STDOUT, $msg.PHP_EOL);
		}
		else {
			echo $msg."<br>";
		}
	}

}