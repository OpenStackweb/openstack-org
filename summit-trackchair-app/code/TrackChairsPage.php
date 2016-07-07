<?php

/*  Used to load the Javascript app that runs the track chairs process
*/

class TrackChairsPage extends Page
{

}


class TrackChairsPage_Controller extends Page_Controller
{

	private static $url_handlers = [
        '$Page/$Action/$ID' => 'handleIndex'
	];

	public function index (SS_HTTPRequest $r) {
		return $this;
	}

    public function init()
    {
    	$parts = parse_url($_SERVER['REQUEST_URI']);
    	$path = $parts['path'];
    	if($path.'/' == $this->Link()) {
    		return $this->redirect(
    			$path.'/'.(isset($parts['query']) ? '?'.$parts['query'] : '')
    		);
    	}

        if (!$this->trackChairCheck()) {
            Security::permissionFailure($this);
        }
        parent::init();
        Requirements::clear();
    }

    public function trackChairCheck()
    {

        $member = Member::currentUser();
        $chair = new SummitTrackChair();

        if ($member) {
            $chair = SummitTrackChair::get()->filter(array(
                'MemberID' => $member->ID,
                'SummitID' => Summit::get_active()->ID
            ));
        }

        if ($chair->exists() || Permission::check('ADMIN')) {
            return true;
        }

    }


    public function JSONConfig()
    {
        return Convert::array2json([
            'baseURL' => $this->Link(),
            'summitID' => Summit::get_active()->ID,
            'pass_order' => SummitSelectedPresentation::config()->pass_order,
            'userinfo' => [
            	'name' => Member::currentUser()->getName(),
            	'email' => Member::currentUser()->Email,
            	'isAdmin' => Permission::check('ADMIN')
            ]
        ]);
    }

    public function IsDev()
    {
        return Director::isDev();
    }


    public function WebpackDevServer() {
        if(Director::isDev()) {
            $socket = @fsockopen('localhost', 3000, $errno, $errstr, 1);
            return !$socket ? false : true;
        }
    }

}