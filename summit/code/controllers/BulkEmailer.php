<?php

class BulkEmailer extends Controller
{

	private static $allowed_actions = [
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
	public function emailattendees(SS_HTTPRequest $r)
	{
	    //TODO: move to crontask !!!
		$startTime = microtime(true);
        $summit_id = $r->getVar('summit_id');
        if(empty($summit_id)){
            die('summit_id is mandatory');
        }
        $summit    = Summit::get()->byId($summit_id);
        if(is_null($summit)){
            die('summit not found!');
        }
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