<?php

class BulkEmailer extends Controller
{

	private static $allowed_actions = [
		'emailspeakers',
		'emailattendees'
	];

	public function init() 
	{
		parent::init();
		if(!Director::is_cli() && !Permission::check('ADMIN')) {
			die('You must be logged in as an admin to use this tool');
		}
	}

	/**
	 * @param SS_HTTPRequest $r
     */
	public function emailspeakers(SS_HTTPRequest $r)
	{
		$summit = Summit::get_most_recent();
		$confirm = $r->getVar('confirm');
		$limit = $r->getVar('limit');
		$speakers = PresentationSpeaker::get()
			->innerJoin('Presentation_Speakers','Presentation_Speakers.PresentationSpeakerID = PresentationSpeaker.ID')
			->innerJoin('SummitEvent', 'SummitEvent.ID = Presentation_Speakers.PresentationID')
			->innerJoin('Presentation', 'Presentation.ID = SummitEvent.ID')
			->exclude([
				// Keynotes, Sponsored Sessions, BoF, and Working Groups, vBrownBag
				'Presentation.CategoryID' => [40, 41, 46, 45, 48]
			])
			->filter([
				'SummitID' => $summit->ID,
				'SummitEvent.Published' => true
			]);
		$totalBeforeLimit = $speakers->count();
		$appliedLimit = $confirm ? null : ($limit ?: 50);
		$speakers =	$speakers->limit($appliedLimit);

		foreach ($speakers as $speaker) {
			/* @var DataList */
			$presentations = $speaker->PublishedPresentations($summit->ID);
				// Todo -- how to deal with this?
				// !$speaker->GeneralOrKeynote() &&
				// !SchedSpeakerEmailLog::BeenEmailed($Speaker->email) &&
			if(!$presentations->exists()) {
				echo "Skipping {$speaker->getName()}. Has no published presentations<br>";
				continue;
			}
			if(!EmailValidator::validEmail($speaker->Member()->Email)) {
				echo $speaker->Member()->Email . " is not a valid email address. Skipping.".$this->br();
				continue;
			}
			
			$to = $speaker->Member()->Email;				
			$subject = "Important Speaker Information for OpenStack Summit in {$summit->Title}";

			$email = EmailFactory::getInstance()->buildEmail('do-not-reply@openstack.org', $to, $subject);
			$email->setUserTemplate("upload-presentation-slides-email");
			$email->populateTemplate([
				'Speaker' => $speaker,
				'Presentations' => $presentations,
				'Summit' => $summit
			]);

			if ($confirm) {
				//SchedSpeakerEmailLog::addSpeaker($to);
				$email->send();
			} else {
				echo $email->debug();
			}

			echo 'Email sent to ' . $to . ' ('.$speaker->getName().')'.$this->br();
		}

		echo $this->br(3) . "Sent a sample of $appliedLimit emails out of $totalBeforeLimit total".$this->br();
	}


	/**
	 * @param SS_HTTPRequest $r
     */
	public function emailattendees(SS_HTTPRequest $r)
	{
		$startTime = microtime(true);
		$summit = Summit::get_most_recent();
		$confirm = $r->getVar('confirm');
		$limit = $r->getVar('limit');		
		$attendees = $summit->Attendees();
		$totalBeforeLimit = $attendees->count();
		$chunkSize = 100;
		$offset = 0;
		$appliedLimit = $confirm ? $chunkSize : ($limit ?: 50);
		$attendees = $attendees->limit($appliedLimit);
		while($offset < $totalBeforeLimit) {
			echo "----- new chunk ($offset) ----".$this->br();
			foreach ($attendees->limit($chunkSize, $offset) as $attendee) {
				if (!EmailValidator::validEmail($attendee->Member()->Email)) {
					echo $attendee->Member()->Email . " is not a valid email. Skipping".$this->br();
					continue;
				}

				$to = $attendee->Member()->Email;
				$subject = "Rate OpenStack Summit sessions from {$summit->Title}";

				$email = EmailFactory::getInstance()->buildEmail('do-not-reply@openstack.org', $to, $subject);
				$email->setUserTemplate("rate-summit-sessions-austin");
				$email->populateTemplate([
					'Name' => $attendee->Member()->FirstName,
				]);

				if ($confirm) {
					$email->send();
				} else {
					//echo $email->debug();
				}

				echo 'Email sent to ' . $to . ' ('.$attendee->Member()->getName().')'.$this->br();

			}
			echo "---- end chunk ($offset) ----".$this->br();
			$offset += $chunkSize;
		}

		echo $this->br(3) ."Sent a sample of $appliedLimit emails out of $totalBeforeLimit total".$this->br();
		$endTime = microtime(true);
		echo "Elapsed time: " . $endTime - $startTime.$this->br();
	}

	protected function br($times = 1)
	{
		$str = Director::is_cli() ? PHP_EOL : "<br>";

		return str_repeat($str, $times);
	}

}