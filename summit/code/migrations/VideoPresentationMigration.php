<?php

class VideoPresentationMigration extends AbstractDBMigrationTask {

    protected $title = "VideoPresentation to PresentationVideo Migration";

    protected $description = "Deprecates the old VideoPresentation table in favor of PresentationMaterial (PresentationVideo). Backfills legacy Summit records and the Presentation records that should relate to them.";

    private static $summits = [
    	'San Diego',
    	'Portland',
    	'Atlanta',
    	'Hong Kong',
    	'Paris',
    	'Vancouver',
    	'Tokyo'
    ];


	/**
	 * Helper method to write context sensitive line breaks
	 * @param  integer $times Number of repetitinos
	 * @return string
	 */
	private function br ($times = 1) {
		return str_repeat(Director::is_cli() ? PHP_EOL : '<br>', $times);
	}


	/**
	 * Guarantees that the old summits that pre-date the Summit object are represented
	 * @return void
	 */
	private function ensureLegacySummits () {
		if(!Summit::get()->filter('Title:PartialMatch', 'San Diego')->first()) {
			$sanDiego = Summit::create([
				'Title' => 'San Diego',
		        'SummitBeginDate' => '2012-10-15 00:00:00',
		        'SummitEndDate' => '2012-10-18 00:00:00',
		        'SubmissionBeginDate' => '2012-08-15 00:00:00',
		        'SubmissionEndDate' => '2012-08-18 00:00:00',
		        'VotingBeginDate' => '2012-08-20 00:00:00',
		        'VotingEndDate' => '2012-08-25 00:00:00',
		        'SelectionBeginDate' => '2012-09-15 00:00:00',
		        'SelectionEndDate' => '2012-09-18 00:00:00',
		        'RegistrationBeginDate' => '2012-10-01 00:00:00',
		        'RegistrationEndDate' => '2012-10-10 00:00:00',
		        'StartShowingVenuesDate' => '2012-05-05 00:00:00',
		        'TimeZone' => '132'
			]);
			$sanDiego->write();
			echo "Created San Diego summit!".$this->br();
		}
		if(!Summit::get()->filter('Title:PartialMatch', 'Portland')->first()) {
			$portland = Summit::create([
				'Title' => 'Portland',
		        'SummitBeginDate' => '2013-04-15 00:00:00',
		        'SummitEndDate' => '2013-04-18 00:00:00',
		        'SubmissionBeginDate' => '2013-01-15 00:00:00',
		        'SubmissionEndDate' => '2013-01-18 00:00:00',
		        'VotingBeginDate' => '2013-02-20 00:00:00',
		        'VotingEndDate' => '2013-02-25 00:00:00',
		        'SelectionBeginDate' => '2013-03-15 00:00:00',
		        'SelectionEndDate' => '2013-03-18 00:00:00',
		        'RegistrationBeginDate' => '2013-04-01 00:00:00',
		        'RegistrationEndDate' => '2013-04-10 00:00:00',
		        'StartShowingVenuesDate' => '2013-01-01 00:00:00',
		        'TimeZone' => '132'
			]);
			$portland->write();
			echo "Created Portland summit!".$this->br();
		}
		if(!Summit::get()->filter('Title:PartialMatch', 'Atlanta')->first()) {
			$atlanta = Summit::create([
				'Title' => 'Atlanta',
		        'SummitBeginDate' => '2013-05-05 00:00:00',
		        'SummitEndDate' => '2013-05-08 00:00:00',
		        'SubmissionBeginDate' => '2013-01-15 00:00:00',
		        'SubmissionEndDate' => '2013-01-18 00:00:00',
		        'VotingBeginDate' => '2013-02-20 00:00:00',
		        'VotingEndDate' => '2013-02-25 00:00:00',
		        'SelectionBeginDate' => '2013-03-15 00:00:00',
		        'SelectionEndDate' => '2013-03-18 00:00:00',
		        'RegistrationBeginDate' => '2013-04-01 00:00:00',
		        'RegistrationEndDate' => '2013-04-10 00:00:00',
		        'StartShowingVenuesDate' => '2013-01-01 00:00:00',
		        'TimeZone' => '151'
			]);
			$atlanta->write();
			echo "Created Atlanta summit!".$this->br();
		}

	}


	/**
	 * Guarantees that a VideoPresentation object has a correct SummitID.
	 * NB: this relies on string matching voodoo, as legacy summits were the wild west.
	 * @param  VideoPresentation
	 * @return VideoPresentation
	 */
	private function ensureSummitID(VideoPresentation $v) {
		$title = $v->PresentationCategoryPage()->Parent()->MenuTitle;
		foreach(self::$summits as $summitName) {
			if(strpos($title, $summitName) !== false) {
				$summit = Summit::get()->filter('Title:PartialMatch', $summitName)->first();
				if(!$summit) {
					echo "$title did not match any summits".$this->br();
					die();
				}
				$v->SummitID = $summit->ID;
				return $v;
			}
		}

		return $v;
	}


	/**
	 * Some VideoPresentation objects have wacky d/m/y dates that are stored as Varchar.
	 * This method fixes those to be Y-m-d H:i:s
	 * 
	 * There are also some records with weird alphanumeric values like "S55", so we throw those out.
	 * 
	 * @param  VideoPresentation $v     
	 * @param  string            $field The date field name to update
	 * @return VideoPresentation
	 */
	private function normaliseDateTime(VideoPresentation $v, $field) {
		if(strpos($v->$field, '/') !== false) {
			if(strpos($v->$field, ':') === false) {
				$v->$field .= ' 00:00';
			}
			$v->$field = DateTime::createFromFormat('j/n/y H:i', $v->$field)->format('Y-m-d H:i:s');
		}
		else {
			$v->$field = null;
		}

		return $v;
	}


	/**
	 * Given a VideoPresentation object, create a Presentation
	 * @param  int            $id A forced ID (SummitEvent and Presentation tables are offset)
	 * @param  VideoPresentation $v 
	 * @return Presentation
	 */
	private function createLegacyPresentation($id, VideoPresentation $v) {
		return Presentation::create([
			'ID' => $id,
			'Title' => $v->Name,
			'Description' => $v->Description,
			'StartDate' => $v->StartTime,
			'EndDate' => $v->EndTime,
			'Published' => true,
			'PublishedDate' => $v->Created,
			'SummitID' => $v->SummitID,
			'Legacy' => true
		]);
	}


	/**
	 * Attempt to add speakers to the presentation by fuzzy matching the FirstName / Surname
	 * @param Presentation      $p 
	 * @param VideoPresentation $v 
	 */
	private function addLegacySpeakers(Presentation $p, VideoPresentation $v) {
		$speakers = explode(',', $v->Speakers);
		foreach($speakers as $speakerName) {
			$speaker = PresentationSpeaker::get()->where("CONCAT(FirstName, ' ', LastName) = '".Convert::raw2sql($speakerName)."'");
			if(!$speaker->exists()) {
				//echo "**** No speakers found! {$v->Speakers}".$this->br();
				$member = Member::get()->where("CONCAT(FirstName, ' ', Surname) = '".Convert::raw2sql($speakerName)."'");
				if($member->exists()) {
					//echo "******* found {$member->count()} members that match the name".$this->br();
				}
			}
			else if($speaker->count() > 1) {
				// echo "**** Multiple speakers found".$this->br();
				// var_dump($speaker->map('Name', 'MemberID')->toArray());
			}
			else {
				$p->Speakers()->add($speaker->first());
				echo "Added speaker " . $speaker->first()->getName();
			}
		}

		return $p;
	}


	/**
	 * Create a PresentationVideo object for a presentation from a VideoPresentation
	 * @param  Presentation      $p 
	 * @param  VideoPresentation $v 
	 * @return PresentationVideo
	 */
	private function createLegacyVideoMaterial(Presentation $p, VideoPresentation $v) {
		return PresentationVideo::create([
			'PresentationID' => $p->ID,
			'Name' => $v->Name,
			'DisplayOnSite' => $v->DisplayOnSite,
			'YouTubeID' => $v->YouTubeID,
			'Featured' => $v->Featured,
			'Created' => $v->LastEdited,
			'LastEdited' => $v->LastEdited
		]);		
	}


	/**
	 * Performs the migration
	 */
	public function doUp () {
		// We're mocking some stuff, e.g. Summit, Presentation, and we need some forgiveness here
		Config::inst()->update('DataObject', 'validation_enabled', false);
		
		// Add the old SD / Portland summits
		$this->ensureLegacySummits();
		echo $this->br(2);

		// Presentations weren't always SummitEvents, so the tables are out of sync.
		// Get the higer of the two ID increments to run a counter and force the ID.
		$maxSummitEventID = DB::query("SELECT MAX(ID) FROM SummitEvent")->value();
		$maxPresentationID = DB::query("SELECT MAX(ID) FROM Presentation")->value();
		$idCounter = max($maxSummitEventID, $maxPresentationID);
		$idCounter++;
		
		$total = VideoPresentation::get()->count();
		$i = 0;
		echo "Migrating VideoPresentations...".$this->br(3);
		$presentationLookup = [];
		foreach(Summit::get() as $s) {
			$presentationLookup[$s->ID] = [];
			foreach($s->Presentations()->sort('Title ASC') as $p) {
				$presentationLookup[$s->ID][
					strtolower(
						preg_replace('/[^a-zA-Z0-9]/', '', $p->Title)
					)
				] = $p->ID;
			}
		}		
		foreach(VideoPresentation::get() as $v) {
			$i++;			
			echo "{$i} / {$total} ....";

			if(!$v->YouTubeID) {
				echo "No YouTubeID. Skipping.".$this->br();
				continue;
			}

			$v = $this->ensureSummitID($v);
			$lookup = $presentationLookup[$v->SummitID];
			$cleanTitle = strtolower(preg_replace('/[^a-zA-Z0-9]/','', $v->Name));
			$originalPresentation = false;
			if(isset($lookup[$cleanTitle])) {
				$originalPresentation = Presentation::get()->byID($lookup[$cleanTitle]);
			}			

			if(!$originalPresentation) {
				echo "********* {$v->Name} has no original presentation!!!".$this->br();

				$v = $this->normaliseDateTime($v, 'StartTime');
				$v = $this->normaliseDateTime($v, 'EndTime');

				$originalPresentation = $this->createLegacyPresentation($idCounter, $v);
				$originalPresentation->write(false, true);
				$idCounter++;
				
				echo "Created presentation {$v->ID} -> {$originalPresentation->ID}...";
				$originalPresentation = $this->addLegacySpeakers($originalPresentation, $v);
			}

			$material = $this->createLegacyVideoMaterial($originalPresentation, $v);
			$material->write();
			$v->write();
			echo "Created video." . $this->br();
		}
	}


	/**
	 * Reverses the migration	 
	 */
	public function doDown () {
		Migration::get()->filter('Name', $this->title)->removeAll();
		PresentationVideo::get()->removeAll();
		if(!PresentationVideo::get()->exists()) {
			echo "Deleted all presentation video materials".$this->br();			
		}
		else {
			echo "*** FAIL: Did not delete presentation video materials".$this->br();			
		}
		foreach(Presentation::get()->filter('Legacy', true) as $p) {
			$p->Speakers()->setByIdList(array ());
			$p->delete();
		}
		if(!Presentation::get()->filter('Legacy', true)->exists()) {
			echo "Deleted all legacy presentations".$this->br();
		}
		else {
			echo "*** FAIL: Did not delete all legacy presentations".$this->br();
		}

		Summit::get()->filter('Title', ['San Diego','Portland', 'Atlanta'])->removeAll();

		if(!Summit::get()->filter('Title', ['San Diego','Portland','Atlanta'])->exists()) {
			echo "Deleted legacy summits".$this->br();
		}
		else {
			echo "*** FAIL: Did not delete legacy summits".$this->br();
		}
	}
}
