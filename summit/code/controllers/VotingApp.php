<?php


class VotingApp extends Controller {

	private static $url_handlers = array (
		'$Action/$ID/$OtherID' => 'index'
	);

    public function index(SS_HTTPRequest $r) {    	
        Requirements::clear();
        return $this;
    }


    public function Link($action = null) {
    	return Controller::join_links(
    		Director::baseURL(),
    		'vote-for-speakers',
 			$action
    	);
    }


    public function AppConfig() {
    	return Convert::array2json(array(
    		'baseUrl' => Director::baseURL(),
    		'appPath' => 'vote-for-speakers',
    		'summitID' => Summit::get_active()->ID
    	));
    }

}