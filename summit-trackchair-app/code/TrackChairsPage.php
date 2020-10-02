<?php

/*  Used to load the Javascript app that runs the track chairs process
*/

class TrackChairsPage extends Page
{

}

class TrackChairsPage_Controller extends Page_Controller
{

    /**
     * @return string
     */
    public function getPageTitle()
    {
       return "Track Chairs App";
    }

    private static $allowed_actions = [
        'selectSummit',
        'handleIndex',
        'handleError',
    ];

	private static $url_handlers = [
        'GET error' => 'handleError',
        'GET summits' => 'selectSummit',
        '$Page/$Action/$ID' => 'handleIndex',
   ];

	public function handleIndex (SS_HTTPRequest $request) {
        $summit_id = intval($request->requestVar('summitID'));

        if(!$summit_id){
            return $this->redirect($this->Link('summits'));
        }
		return $this;
	}

	public function handleError(SS_HTTPRequest $request){
        JQueryCoreDependencies::renderRequirements();
        Page_Controller::AddRequirements();
        Requirements::css('summit-trackchair-app/css/summits.css');

        return $this->customise([
            'backURL' => urlencode(sprintf('/track-chairs/summits'))
        ])->renderWith(
            [
                'TrackChairsPage_error',
                'TrackChairsPage_error'
            ],
            $this
        );
    }

	public function selectSummit(SS_HTTPRequest $request){

        $summit_id = intval($request->requestVar('summitID'));

        if($summit_id > 0){
            return $this->redirect(sprintf('%s?summitID=%s', $this->Link(), $summit_id));
        }

        JQueryCoreDependencies::renderRequirements();
	    Requirements::javascript("summit-trackchair-app/javascript/summits.js");
	    Page_Controller::AddRequirements();
	    Requirements::css('summit-trackchair-app/css/summits.css');
        return $this->customise([
            'backURL' => urlencode(sprintf('/track-chairs/summits'))
        ])->renderWith(
            [
                'TrackChairsPage_summits',
                'TrackChairsPage_summits'
            ],
            $this
        );
    }

    public function getAvailableSummits(){
        $plans = SelectionPlan::get()
            ->where('Enabled = 1 AND SelectionBeginDate <= UTC_TIMESTAMP() AND SelectionEndDate >= UTC_TIMESTAMP() ')
            ->sort('SelectionBeginDate ASC');
        $res = new ArrayList();
        foreach ($plans as $plan){
            $res->push($plan->Summit());
        }
        return $res;
    }

    public function init()
    {
        parent::init();
    	$parts = parse_url($_SERVER['REQUEST_URI']);
    	$path = $parts['path'];
    	if($path.'/' == $this->Link()) {
    		return $this->redirect(
    			$path.'/'.(isset($parts['query']) ? '?'.$parts['query'] : '')
    		);
    	}

    	$currentSummitId = $this->getCurrentSummitId();
        if(!TrackChairsAuthorization::authorize($currentSummitId))
        {
            if(!$currentSummitId) {
                return Security::permissionFailure($this);
            }
            return $this->redirect($this->Link('error'));
        }

        Requirements::clear();
        Requirements::css(Director::protocol() . '://maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css?'.time());
        Requirements::css("marketplace/code/ui/frontend/css/star-rating.min.css");
        Requirements::css('node_modules/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css');
    }

    public function getCurrentSummitId():int{
        $summit_id = intval($this->request->requestVar('summitID'));
        return intval($summit_id);
    }

    public function JSONConfig()
    {
        $summit = Summit::get()->byID($this->getCurrentSummitId());
        $plan = $summit->getOpenSelectionPlanForStage();
        return Convert::array2json([
            'baseURL' => $this->Link(),
            'summitID' => $summit->ID,
            'summitName' => sprintf("%s - %s", $summit->Title, $plan->Name),
            'selectionLink' => $this->Link('summits'),
            'backURL' => urlencode(sprintf('/track-chairs/summits?summitID=%s', $summit->ID)),
            'pass_order' => SummitSelectedPresentation::config()->pass_order,
            'userinfo' => [
            	'name' => Member::currentUser()->getName(),
            	'email' => Member::currentUser()->Email,
            	'isAdmin' => TrackChairsAuthorization::isAdmin($summit->ID)
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