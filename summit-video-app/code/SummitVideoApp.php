<?php

class SummitVideoApp extends Page {

	public function canCreate ($member = null) {
		return !self::get()->exists();
	}	

}


class SummitVideoApp_Controller extends Page_Controller {
	

    private static $url_handlers = [
        'api/video/$Type' => 'handleVideo',
        'api/videos' => 'handleVideos',
        'api/summits' => 'handleSummits',
        'api/speakers' => 'handleSpeakers',
        'simulateupload' => 'handleSimulateUpload',
        'PUT api/view/$VideoID' => 'handleVideoViewed',
        '$Page/$Action/$ID' => 'handleIndex'
    ];

    
    private static $allowed_actions = [
        'handleVideos',
        'handleVideo',
        'handleSummits',
        'handleSpeakers',
        'handleVideoViewed',
        'handleIndex',
        'handleSimulateUpload' => 'ADMIN'
    ];


	protected $initialState = [
		'videos' => [
			'allVideos' => [
				'results' => []
			],
			'summitVideos' => [
				'summit' => null,
				'results' => []
			],
			'speakerVideos' => [
				'speaker' => null,
				'results' => []
			],
			'highlightedVideos' => [
				'results' => []
			],
			'popularVideos' => [
				'results' => []
			],
			'searchVideos' => [
				'results' => null,
				'activeTab' => 'titleMatches'
			]
		],
		'video' => [
			'featuredVideo' => null,
			'latestVideo' => null,
		],
		'videoDetail' => [
			'video' => null
		],
		'summits' => [
			'loading' => false,
			'results' => []
		],
		'speakers' => [
			'loading' => false,
			'results' => []
		],
	];


	protected $backend;


	public function __construct($dataRecord = null) 
	{
		parent::__construct($dataRecord);
		$this->backend = Injector::inst()->get('SummitVideoAppBackend');
	}

	public function init () 
	{
		parent::init();
		if(!$this->WebpackDevServer()) {
			Requirements::css('summit-video-app/production/css/main.css');
		}
	}


	public function getJSONConfig () 
	{
		$config = [
			'baseURL' => rtrim($this->Link(),'/'),
			'initialState' => $this->getInitialState(),
			'pollInterval' => SummitVideoApp::config()->video_poll_interval,
            'securityToken' => SecurityToken::inst()->getValue(),
            'videoViewDelay' => SummitVideoApp::config()->video_view_delay			
		];

		return Convert::array2json($config);
	}


	public function handleIndex($r) 
	{
		return $this;
	}


	public function handleVideo(SS_HTTPRequest $r) 
	{
		$type = $r->param('Type');
		$result = null;

		if($type === 'latest') {
			$result = $this->backend->getLatestVideo();
		}
		else if($type === 'featured') {
			$result = $this->backend->getFeaturedVideo();
		}
		else {
			$result = $this->backend->getVideoDetail($type);
		}

		if(!$result) $result = [];

		return $this->respondJSON($result);
	}


    public function handleVideoViewed(SS_HTTPRequest $r) 
    {
    	if(!SecurityToken::inst()->checkRequest($r)) {
    		return $this->httpError(403, 'Invalid token');
    	}
    	$videoID = $r->param('VideoID');
		$video = PresentationVideo::get()->filter('Presentation.Slug', $videoID)->first();
    	
    	if(!$video) {
    		$video = PresentationVideo::get()->byID($videoID);
    	}

    	if(!$video) {
    		return $this->httpError(404);
    	}
    	
    	if(!$this->canEditVideo($video->ID)) {
    		return $this->httpError(403, 'Too many requests');
    	}

		$video->Views++;
		$video->write();
		$this->setVideoViewed($video->ID);

		return new SS_HTTPResponse('OK', 200);
    }


	public function handleVideos(SS_HTTPRequest $r) 
	{
		return $this->respondJSON(
			$this->backend->getVideos($r->getVars())
		);
	}


	public function handleSummits(SS_HTTPRequest $r) 
	{
		return $this->respondJSON(
			$this->backend->getSummits($r->getVars())
		);
	}


	public function handleSpeakers(SS_HTTPRequest $r) 
	{
		return $this->respondJSON(
			$this->backend->getSpeakers($r->getVars())
		);
	}


	protected function getInitialState () 
	{
		$state = $this->initialState;
		$page = $this->request->param('Page');
		$action = $this->request->param('Action');
		$id = $this->request->param('ID');
		$backend = $this->backend;

		$state['video']['latestVideo'] = $backend->getLatestVideo();
		
		switch($page) {
			case "summits":				
				$state['summits'] = $backend->getSummits();
				$state['videos']['summitVideos'] = $backend->getVideos(['summit' => $id]);
				break;
			case "speakers":
				$state['speakers'] = $backend->getSpeakers(
					['letter' => $this->request->getVar('letter')]
				);
				$state['videos']['speakerVideos'] = $backend->getVideos(['speaker' => $id]);
				break;
			case "featured":
				$state['videos']['highlightedVideos'] = $backend->getVideos(['highlighted' => true]);
				$state['videos']['popularVideos'] = $backend->getVideos(['popular' => true]);
				$state['video']['featuredVideo'] = $backend->getFeaturedVideo();
				break;
			case "video":
				$state['videoDetail'] = [
					'video' => $backend->getVideoDetail($id)
				];
				break;
			case "search":
				$state['videos']['searchVideos'] = $backend->getVideos(
					['search' => $this->request->getVar('search')]
				);
				$state['videos']['searchVideos']['activeTab'] = 'titleMatches';
				break;
			default:
				$state['videos']['allVideos'] = $backend->getVideos();
				break;
			
		}

		return $state;
	}


	protected function respondJSON($response = array ()) 
	{
		return (new SS_HTTPResponse(Convert::array2json($response), 200))
					->addHeader('Content-Type', 'application/json');		
	}


    protected function canEditVideo($videoID)
    {
    	$state = Session::get('VideoView');
    	if(!$state) return true;

    	list($videoID, $lastViewed) = explode('_', $state);
    	$maxFrequency = SummitVideoApp::config()->video_view_throttle;

    	return !($videoID == $videoID && (time() - $lastViewed < $maxFrequency));
    }


    protected function setVideoViewed($videoID) 
    {
    	Session::set('VideoView', $videoID.'_'.time());
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

    public function handleSimulateUpload(SS_HTTPRequest $r) 
    {
		Config::inst()->update('DataObject', 'validation_enabled', false);
    	$rand = PresentationVideo::get()->sort('RAND()')->first();
    	$vid = $rand->duplicate();
    	$vid->Name = $r->getVar('title') ? $r->getVar('title') : 'New video ' . uniqid();    	
    	DB::query("INSERT INTO SummitEvent SET ClassName = 'Presentation', Created = '".date('Y-m-d H:i:s')."', Title = '{$vid->Name}', SummitID = 6");
    	$id = DB::get_generated_id("SummitEvent");
    	DB::query("INSERT INTO Presentation SET ClassName='Presentation', ID = $id, Slug='temp-video-${id}', Legacy=1");
    	$vid->PresentationID = $id;
    	$vid->forceChange();
    	$vid->write();
    	die('OK');
    }	
}