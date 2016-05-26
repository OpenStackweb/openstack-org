<?php

class BulkEmailer extends Controller
{

	private static $allowed_actions = [
		'emailspeakers' => 'ADMIN',
		'emailattendees' => 'ADMIN'
	];

	public function init() 
	{
		parent::init();
		if(!Permission::check('ADMIN')) {
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
				echo $speaker->Member()->Email . " is not a valid email address. Skipping<br>";
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

			echo 'Email sent to ' . $to . ' ('.$speaker->getName().')<br/>';
		}

		echo "<br><br><br>Sent a sample of $appliedLimit emails out of $totalBeforeLimit total<br />";
	}


	/**
	 * @param SS_HTTPRequest $r
     */
	public function emailattendees(SS_HTTPRequest $r)
	{
		$summit = Summit::get_most_recent();
		$confirm = $r->getVar('confirm');
		$limit = $r->getVar('limit');		
		$attendees = $summit->Attendees();
		$totalBeforeLimit = $attendees->count();
		$appliedLimit = $confirm ? null : ($limit ?: 50);
		$attendees = $attendees->limit($appliedLimit);

		foreach ($attendees as $attendee) {
			if (!EmailValidator::validEmail($attendee->Member()->Email)) {
				echo $attendee->Member()->Email . " is not a valid email. Skipping";
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
				//SchedSpeakerEmailLog::addSpeaker($to);
				$email->send();
			} else {
				echo $email->debug();
			}

			echo 'Email sent to ' . $to . ' ('.$attendee->Member()->getName().')<br/>';

		}
		
		echo "<br><br><br>Sent a sample of $appliedLimit emails out of $totalBeforeLimit total<br />";		
	}


}