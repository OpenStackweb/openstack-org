<?php

/**
 * Class UpdateFeedTask
 */
final class UpdateFeedTask extends CliController {

	function process(){

		set_time_limit(0);

		try{
			$home = DataObject::get_one('HomePage');
			if($home){
				$url            = Director::absoluteURL('feeds/openstack.php');
				$data           = file_get_contents($url);
				$home->FeedData = $data; // context of _controller makes expanding to dataRecond nessesary
				$home->write();
				echo 'OK';
			}
		}
		catch(Exception $ex){
			SS_Log::log($ex,SS_Log::ERR);
			echo $ex->getMessage();
		}
	}
} 