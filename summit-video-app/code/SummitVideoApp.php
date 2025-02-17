<?php

/**
 * Class SummitVideoApp
 */
class SummitVideoApp extends Page
{

    /**
     * @param null $member
     * @return bool
     */
    public function canCreate($member = null)
    {
        return !self::get()->exists();
    }

    public function getOGDescription()
    {
        if($this->hasField('MetaTitle')) {
            $title = trim($this->MetaTitle);
            if(!empty($title)) return $title;
        }
        return 'Videos from OpenStack Summits';
    }

}


/**
 * Class SummitVideoApp_Controller
 */
class SummitVideoApp_Controller extends Page_Controller
{

    /**
     * @var array
     */
    private static $url_handlers = [
        'api/video/$Type'               => 'handleVideo',
        'api/videos'                    => 'handleVideos',
        'api/summits'                   => 'handleSummits',
        'api/speakers'                  => 'handleSpeakers',
        'PUT api/view/$VideoID'         => 'handleVideoViewed',
        'summits/$Summit/$Page/$ID'     => 'handleIndex',
        '$Summit/$Page/$ID'             => 'handleIndex',
        '$Page/$ID'                     => 'handleIndex'
    ];

    /*
     *    'api/video/$Type' => 'handleVideo',
 -        'api/videos' => 'handleVideos',
 -        'api/summits' => 'handleSummits',
 -        'api/speakers' => 'handleSpeakers',
          '$Page/$Action/$ID' => 'handleIndex'
     */


    /**
     * @var array
     */
    private static $allowed_actions = [
        'handleVideos',
        'handleVideo',
        'handleSummits',
        'handleSpeakers',
        'handleVideoViewed',
        'handleIndex',
    ];


    /**
     * @var array
     */
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
            ],
            'tagVideos' => [
                'tag' => null,
                'results' => []
            ],
            'trackVideos' => [
                'track' => null,
                'results' => []
            ],
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


    /**
     * @var
     */
    protected $backend;


    /**
     * SummitVideoApp_Controller constructor.
     * @param null $dataRecord
     */
    public function __construct($dataRecord = null)
    {
        parent::__construct($dataRecord);
        $this->backend = Injector::inst()->get('SummitVideoAppBackend');
    }
    

    /**
     * @return mixed
     */
    public function getJSONConfig()
    {
        $config = [
            'baseURL' => rtrim($this->RelativeLink(), '/'),
            'initialState' => $this->getInitialState(),
            'pollInterval' => SummitVideoApp::config()->video_poll_interval,
            'securityToken' => SecurityToken::inst()->getValue()
        ];

        return Convert::array2json($config);
    }


    /**
     * @param $r
     * @return $this
     */
    public function handleIndex($r)
    {
        return $this;
    }


    /**
     * @param SS_HTTPRequest $r
     * @return mixed
     */
    public function handleVideo(SS_HTTPRequest $r)
    {
        $type = $r->param('Type');
        $result = null;

        if ($type === 'latest') {
            $result = $this->backend->getLatestVideo();
        } else {
            if ($type === 'featured') {
                $result = $this->backend->getFeaturedVideo();
            } else {
                $result = $this->backend->getVideoDetail($type);
            }
        }

        if (!$result) {
            $result = [];
        }

        return $this->respondJSON($result);
    }


    /**
     * @param SS_HTTPRequest $r
     * @return mixed
     */
    public function handleVideos(SS_HTTPRequest $r)
    {
        return $this->respondJSON(
            $this->backend->getVideos($r->getVars())
        );
    }

    /**
     * @param SS_HTTPRequest $r
     * @return mixed
     */
    public function handleSummits(SS_HTTPRequest $r)
    {
        return $this->respondJSON(
            $this->backend->getSummits($r->getVars())
        );
    }


    /**
     * @param SS_HTTPRequest $r
     * @return mixed
     */
    public function handleSpeakers(SS_HTTPRequest $r)
    {
        return $this->respondJSON(
            $this->backend->getSpeakers($r->getVars())
        );
    }


    /**
     * @return array
     */
    protected function getInitialState()
    {
        $state = $this->initialState;
        $page = $this->request->param('Page');
        $id = $this->request->param('ID');
        $backend = $this->backend;

        $state['video']['latestVideo'] = $backend->getLatestVideo();

        switch ($page) {
            case "summits":
                $state['summits'] = $backend->getSummits();
                $state['videos']['summitVideos'] = $backend->getVideos(['group' => 'summit', 'id' => $id]);
                break;
            case "tags":
                $state['videos']['tagVideos'] = $backend->getVideos(['group' => 'tag', 'id' => $id]);
                break;
            case "tracks":
                $summit = $this->request->param('Summit');
                $state['videos']['trackVideos'] = $backend->getVideos(['group' => 'track', 'id' => $id, 'summit' => $summit]);
                break;
            case "speakers":
                $state['speakers'] = $backend->getSpeakers(
                    ['letter' => $this->request->getVar('letter')]
                );
                $state['videos']['speakerVideos'] = $backend->getVideos(['group' => 'speaker', 'id' => $id]);
                break;
            case "featured":
                $state['videos']['highlightedVideos'] = $backend->getVideos(['group' => 'highlighted']);
                $state['videos']['popularVideos'] = $backend->getVideos(['group' => 'popular']);
                $state['video']['featuredVideo'] = $backend->getFeaturedVideo();
                break;
            case "search":
                $state['videos']['searchVideos'] = $backend->getVideos(
                    ['group' => 'search', 'search' => $this->request->getVar('search')]
                );
                $state['videos']['searchVideos']['activeTab'] = 'titleMatches';
                break;
            default:
                $summit = Summit::get()->filter('Slug', $page)->first();
                if ($summit) {
                    $state['videoDetail'] = [
                        'video' => $backend->getVideoDetail($id)
                    ];
                } else {
                    $state['videos']['allVideos'] = $backend->getVideos();
                }

                break;

        }

        return $state;
    }


    /**
     * @param array $response
     * @return mixed
     */
    protected function respondJSON($response = array())
    {
        return (new SS_HTTPResponse(Convert::array2json($response), 200))
            ->addHeader('Content-Type', 'application/json');
    }


    /**
     * @return mixed
     */
    public function IsDev()
    {
        return Director::isDev();
    }


    /**
     * @return bool
     */
    public function WebpackDevServer()
    {
        if (Director::isDev()) {
            $socket = @fsockopen('localhost', 3000, $errno, $errstr, 1);
            return !$socket ? false : true;
        }
    }

    public function MetaTags()
    {
        $summit_slug       = $this->request->param('Action');
        $presentation_slug = $this->request->param('ID');
        $summit            = Summit::get()->filter('Slug', trim($summit_slug))->first();

        if ($summit && !empty($presentation_slug)) {
            $video = PresentationVideo::get()->filter([
                'Presentation.Slug' => trim($presentation_slug),
            ])->first();

            if(!is_null($video)) {
                $tags = "<meta name=\"title\" content=\"" . Convert::raw2att($video->Name) . "\" />".PHP_EOL;
                $description = $video->getSocialSummary();
                if(!empty($description))
                    $tags .= "<meta name=\"description\" content=\"" . Convert::raw2att($description) . "\" />".PHP_EOL;
                $tags .= $video->MetaTags();
                return $tags;
            }
        }

        return parent::MetaTags();
    }

}