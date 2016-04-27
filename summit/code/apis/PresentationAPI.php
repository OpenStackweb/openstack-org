<?php 

class PresentationAPI extends Controller {


	private static $url_handlers = array (
		'summit/$ID' => 'handleSummit',
		'presentation/$ID' => 'handleManagePresentation',		
		'GET ' => 'handleGetAllPresentations',

	);


	private static $allowed_actions = array (
		'handleSummit',
		'handleManagePresentation',
		'handleGetAllPresentations',
	);


	private static $extensions = array (
		'MemberTokenAuthenticator'
	);


	public function init() {		
		parent::init();		
		$this->checkAuthenticationToken(false);
	}
	

	public function handleSummit(SS_HTTPRequest $r) {
		if($r->param('ID') == "active") {
			$summit = Summit::get_active();
		}
		else {
			$summit = Summit::get()->byID($r->param('ID'));
		}

		if(!$summit) return $this->httpError(404, "That summit could not be found.");
		if(!$summit->exists()) return $this->httpError(404, "There is no active summit.");

		$data = $summit->toJSON();
		$data['status'] = $summit->getStatus();
		$data['categories'] = array ();

		foreach($summit->Categories() as $c) {
			$data['categories'][] = $c->toJSON();
		}
		
    	return (new SS_HTTPResponse(
    		Convert::array2json($data), 200
    	))->addHeader('Content-Type', 'application/json');



	}


	public function handleGetAllPresentations(SS_HTTPRequest $r) {
		$limit = $r->getVar('limit') ?: 50;
		if($limit > 50) $limit = 50;

		$start = $r->getVar('page') ?: 0;

		$presentations = Member::currentUser() ? 
							Member::currentUser()->getRandomisedPresentations() : 
							Presentation::get()->filter(array(
								'SummitEvent.SummitID' => Summit::get_active()->ID
							));
							
		if($r->getVar('category')) {
			$presentations = $presentations->filter('CategoryID',(int) $r->getVar('category'));
		}
		if($r->getVar('keyword')) {
			$k = $r->getVar('keyword');
			$presentations = $presentations->filterAny(array(
				'Title:PartialMatch' => $k,
				'Description:PartialMatch' => $k,
				'Speakers.FirstName:PartialMatch' => $k,
				'Speakers.LastName:PartialMatch' => $k
			));
		}
		if($r->getVar('voted') == "true") {
			$presentations = $presentations
								->leftJoin("PresentationVote", "PresentationVote.PresentationID = Presentation.ID")
								->where("IFNULL(PresentationVote.MemberID,0) = " . Member::currentUserID());
		}
		if($r->getVar('voted') == "false") {
			$presentations = $presentations
								->leftJoin("PresentationVote", "PresentationVote.PresentationID = Presentation.ID")
								->where("IFNULL(PresentationVote.MemberID,0) != " . Member::currentUserID());
		}

		$count = $presentations->count();
		$presentations = $presentations->limit($limit, $start*$limit);

		$data = array (
			'results' => array (),
			'has_more' => $count > ($limit * ($start+1)),
			'total' => $count,
			'remaining' => $count - ($limit * ($start+1))
		);

		foreach($presentations as $p) {			
			$data['results'][] = array(
        		'id' => $p->ID,
            	'title' => $p->Title,            
            	'user_vote' => $p->getUserVote() ? $p->getUserVote()->Vote : null
    		);
		}

    	return (new SS_HTTPResponse(
    		Convert::array2json($data), 200
    	))->addHeader('Content-Type', 'application/json');
	}


	public function handleManagePresentation(SS_HTTPRequest $r) {

		if($presentation = Presentation::get()->byID($r->param('ID'))) {
			$request = PresentationAPI_PresentationRequest::create($presentation, $this);

			return $request->handleRequest($r, DataModel::inst());
		}

		return $this->httpError(404, "Presentation " . $r->param('ID') . " not found");
	}


}


class PresentationAPI_PresentationRequest extends RequestHandler {

	
	private static $url_handlers = array (
		'GET ' => 'index',
		'POST vote' => 'handleVote',
		'PUT view' => 'handleView',
		'POST video' => 'handleApplyVideo'
	);


	private static $allowed_actions = array (
		'handleVote',
		'handleView',
		'handleApplyVideo'
	);


	protected $presentation;


	protected $parent;


	public function __construct(Presentation $presentation, PresentationAPI $parent) {
		parent::__construct();
		$this->presentation = $presentation;
		$this->parent = $parent;
	}


	public function index(SS_HTTPRequest $r) {
		$p = $this->presentation;
    	$speakers = array ();

    	foreach($p->Speakers() as $s) {
    		$photo_url = null;
    		if($s->Photo()->exists() && $s->Photo()->croppedImage(100,100)) {
    			$photo_url = $s->Photo()->croppedImage(100,100)->URL;
    		}

    		$speakerData = $s->toJSON();
    		$speakerData['photo_url'] = $photo_url;
    		$speakers[] = $speakerData;

    	}

        $data = $p->toJSON();        
        $data['speakers'] = $speakers;
        $data['total_votes'] = $p->Votes()->count();
        $data['average_vote'] = (int) $p->Votes()->avg('Vote');
        $data['creator'] = $p->Creator()->getName();
        $data['user_vote'] = $p->getUserVote() ? $p->getUserVote()->Vote : null;
        $data['can_vote'] = !!Member::currentUser();

    	return (new SS_HTTPResponse(
    		Convert::array2json($data), 200
    	))->addHeader('Content-Type', 'application/json');

	}


	public function handleVote(SS_HTTPRequest $r) {
		if(!Member::currentUser()) {
			return $this->httpError(403);
		}
		
		$vote = $r->postVar('vote');
		if($vote >= -1 && $vote <= 3) {
			$this->presentation->setUserVote($vote);

			return new SS_HTTPResponse(null, 200);
		}

		return $this->httpError(400, "Invalid vote");
	}


	public function handleView(SS_HTTPRequest $r) {
		$this->presentation->Views++;
		$this->presentation->write();
		
		return new SS_HTTPResponse(null, 200);
	}


	public function handleApplyVideo(SS_HTTPRequest $r) {		

		if(!Member::currentUser()) {			
			return $this->httpError(403);
		}

		// Only allow one writeable property here
		if($youTube = $r->postVar('youtubeid')) {
			$video = $this->presentation->Materials()
							->filter('ClassName', 'PresentationVideo')
							->first();
			if(!$video) {
				$video = PresentationVideo::create();					
			}

			$video->PresentationID = $this->presentation->ID;
			$video->DateUploaded = SS_Datetime::now()->Rfc2822();
			$video->Name = $this->presentation->Title;
			$video->DisplayOnSite = true;
			$video->YouTubeID = $youTube;
			$video->write();

			return new SS_HTTPResponse("OK", 200);			
		}

		return $this->httpError(400, "You must provide a youtubeid parameter in the POST request");
	}

}