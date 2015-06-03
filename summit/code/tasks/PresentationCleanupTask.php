<?php

/**
 * This task removes any presentations that were started and never submitted.
 *
 * @package  presentations
 * @author  Aaron Carlino <unclecheese@leftandmain.com>
 * 
 */
class PresentationCleanupTask extends CliController {

	/**
	 * @var string $title Shown in the overview on the {@link TaskRunner}
	 * HTML or CLI interface. Should be short and concise, no HTML allowed.
	 */
	protected $title = "Presentation cleanup task";
	
	/**
	 * @var string $description Describe the implications the task has,
	 * and the changes it makes. Accepts HTML formatting.
	 */
	protected $description = 'Removes abandonned presentations';
	

	/**
	 * Run the task
	 * @param  SS_HTTPRequest $request 	 
	 */
	public function index() {		
		$list = Presentation::get()->filter(array(
			'Progress' => 0
		));

		$count = $list->count();

		$list->removeAll();

		$this->writeOut("Deleted $count presentations");
	}

	protected function writeOut($msg) {
		if(Director::is_cli()) {
			fwrite(STDOUT, $msg.PHP_EOL);
		}
		else {
			echo $msg."<br>";
		}
	}

}