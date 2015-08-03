<?php 

class TrackChairAPI extends Controller {


	private static $url_handlers = array (
		'summit/$ID' => 'handleSummit',
		'POST presentation/sched/$SchedID!' => 'handleSchedUpdate',
		'presentation/$ID' => 'handleManagePresentation',		
		'GET ' => 'handleGetAllPresentations',
		'GET selections/$categoryID' => 'handleGetMemberSelections',
		'POST reorder' => 'handleReorderList',
		'GET category/$ID/change_requests' => 'handleChangeRequests',
		'GET chair/add' => 'handleAddChair'
	);


	private static $allowed_actions = array (
		'handleSummit',
		'handleManagePresentation',
		'handleGetAllPresentations',
		'handleSchedUpdate',
		'handleGetMemberSelections',
		'handleReorderList',
		'handleChangeRequests',
		'handleAddChair' => 'ADMIN'
	);


	private static $extensions = array (
		'MemberTokenAuthenticator'
	);


	public function init() {
		parent::init();		
		$this->checkAuthenticationToken(false);
	}

	private function trackChairDetails() {

		if(!Member::currentUser()) {
			return $this->httpError(403);
		}

		$data = array (
			'categories' => NULL,
		);

		$TrackChair = SummitTrackChair::get()->filter('MemberID', Member::currentUserID());

		if($TrackChair->count()) {

			$chair = $TrackChair->first();

			$data['first_name'] = $chair->Member()->FirstName;
			$data['last_name'] = $chair->Member()->Surname;

			foreach($chair->Categories() as $c) {
				$data['categories'][] = $c->toJSON();
			}
		}

    	return $data;

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
		$data['track_chair'] = $this->trackChairDetails();

		foreach($summit->Categories()->filter('ChairVisible', TRUE) as $c) {
			$data['categories'][] = $c->toJSON();
		}
		
    	return (new SS_HTTPResponse(
    		Convert::array2json($data), 200
    	))->addHeader('Content-Type', 'application/json');



	}


	public function handleGetAllPresentations(SS_HTTPRequest $r) {
		$limit = $r->getVar('limit') ?: 2000;
		if($limit > 2000) $limit = 2000;

		$start = $r->getVar('page') ?: 0;

		$summitID = Summit::get_active()->ID;

		// Get a collection of chair-visible presentation categories
		$presentations = Presentation::get()
			->leftJoin("PresentationCategory", "PresentationCategory.ID = Presentation.CategoryID")
			->where("Presentation.SummitID = {$summitID} AND PresentationCategory.ChairVisible = 1");

		if($r->getVar('category')) {
			$presentations = $presentations->filter('CategoryID',(int) $r->getVar('category'));
		}
		if($r->getVar('keyword')) {
			$k = Convert::raw2sql($r->getVar('keyword'));
			$presentations = $presentations
								->leftJoin("Presentation_Speakers", "Presentation_Speakers.PresentationID = Presentation.ID")
								->leftJoin("PresentationSpeaker", "PresentationSpeaker.ID = Presentation_Speakers.PresentationSpeakerID")
								->where("
									Presentation.Title LIKE '%{$k}%' 
									OR Presentation.Description LIKE '%{$k}%'
									OR PresentationSpeaker.FirstName LIKE '%{$k}%'
									OR PresentationSpeaker.LastName LIKE '%{$k}%'
								");
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
            	'selected' => $p->isSelected(),
            	'vote_count' => $p->CalcVoteCount(),
            	'vote_average' => $p->CalcVoteAverage(),
            	'total_points' => $p->CalcTotalPoints()
    		);
		}

    	return (new SS_HTTPResponse(
    		Convert::array2json($data), 200
    	))->addHeader('Content-Type', 'application/json');
	}


	public function handleManagePresentation(SS_HTTPRequest $r) {

		/* if(!Member::currentUser()) {
			return $this->httpError(403);
		} */

		if($presentation = Presentation::get()->byID($r->param('ID'))) {
			$request = TrackChairAPI_PresentationRequest::create($presentation, $this);

			return $request->handleRequest($r, DataModel::inst());
		}

		return $this->httpError(404);
	}



	public function handleGetMemberSelections(SS_HTTPRequest $r) {

		if(!Member::currentUser()) {
			return $this->httpError(403);
		}		

		$results = [];
		$categoryID = (int) $r->param('categoryID');
		$results['category_id'] = $categoryID;

		$lists = SummitSelectedPresentationList::getAllListsByCategory($categoryID);


		foreach ($lists as $key => $list) {

				$selections = $list->SummitSelectedPresentations()->sort('Order ASC');
				$count = $selections->count();
				$listID = $list->ID;

				$data = array (
					'list_name' => $list->name,
					'list_type' => $list->ListType,
					'list_id' => $listID,
					'total' => $count,
					'can_edit' => $list->memberCanEdit(),
					'slots' => $list->maxPresentations(),
					'mine' => $list->mine()
				);

				foreach($selections as $s) {			
					$data['selections'][] = array(
		            	'title' => $s->Presentation()->Title,
		            	'order' => $s->Order,
		            	'id' => $s->PresentationID
		    		);
				}

				$results['lists'][] = $data;

		}

    	return (new SS_HTTPResponse(
    		Convert::array2json($results), 200
    	))->addHeader('Content-Type', 'application/json');



	}

	public function handleReorderList(SS_HTTPRequest $r) {

		$sortOrder = $r->postVar('sort_order');
		$listID = $r->postVar('list_id');
		$list = SummitSelectedPresentationList::get()->byId($listID);
    	
		if(!$list->memberCanEdit()) return new SS_HTTPResponse(null, 403); 

    	if(is_array($sortOrder)) {
    		foreach ($sortOrder as $key=>$id) {
    			$selection = SummitSelectedPresentation::get()->filter(array(
    				'PresentationID' => $id,
    				'SummitSelectedPresentationListID' => $listID
    			));

    			// Add the selection if it's new
    			if(!$selection->exists()) {
    				$presentation = Presentation::get()->byId($id);
    				if($presentation->exists() && $presentation->CategoryID == $list->CategoryID) {
    					$s = new SummitSelectedPresentation();
    					$s->SummitSelectedPresentationListID = $listID;
    					$s->PresentationID = $presentation->ID;
    					$s->MemberID = Member::currentUserID();
    					$s->Order = $key + 1;
    					$s->write();
    				}

    			}

				// Adjust the order if not
    			if($selection->exists()) {
    				$s = $selection->first();
    				$s->Order = $key + 1;
    				$s->write();
    			}
    		}
    	}

    	return new SS_HTTPResponse(null, 200);

	}

	public function handleChangeRequests(SS_HTTPRequest $r) {

		$catid = $r->param('ID');

		$changeRequests = SummitCategoryChange::get()->filter(array(
			'NewCategoryID' => $catid,
			'Done' => false
		));

		$results = [];

		foreach ($changeRequests as $request) {

			$data = [];
			$data['presentation_id'] = $request->PresentationID;
			$data['presentation_title'] = $request->Presentation()->Title;

			$results[] = $data;
		}

    	return (new SS_HTTPResponse(
    		Convert::array2json($results), 200
    	))->addHeader('Content-Type', 'application/json');

	}

	public function handleAddChair(SS_HTTPRequest $r) {
		$email = $r->getVar('email');
		$catid = $r->getVar('cat_id');
		$category = PresentationCategory::get()->byID($catid);
		$member = Member::get()->filter('Email', $email)->first();

		SummitTrackChair::addChair($member, $catid);
		$category->MemberList($member->ID);
		$category->GroupList();
	}

}


class TrackChairAPI_PresentationRequest extends RequestHandler {

	
	private static $url_handlers = array (
		'GET ' => 'index',
		'POST vote' => 'handleVote',
		'POST comment' => 'handleAddComment',
		'GET select' => 'handleSelect',
		'GET unselect' => 'handleUnselect',
		'GET category_change/new' => 'handleCategoryChangeRequest',
		'GET category_change/accept' => 'handleAcceptCategoryChange'
	);


	private static $allowed_actions = array (
		'handleVote',
		'handleAddComment',
		'handleSelect',
		'handleUnselect',
		'handleCategoryChangeRequest',
		'handleAcceptCategoryChange'
	);


	protected $presentation;


	protected $parent;


	public function __construct(Presentation $presentation, PresentationAPI $parent) {
		parent::__construct();
		$this->presentation = $presentation;
		$this->parent = $parent;
	}


	public function index(SS_HTTPRequest $r) {

		/* if(!Member::currentUser()) {
			return $this->httpError(403);
		} */

		$p = $this->presentation;
    	$speakers = array ();

    	foreach($p->Speakers() as $s) {
    		$s->Bio = str_replace(array("\r", "\n"), "", $s->Bio);
    		$photo_url = null;
    		if($s->Photo()->exists() && $s->Photo()->croppedImage(100,100)) {
    			$photo_url = $s->Photo()->croppedImage(100,100)->URL;
    		}

    		$speakerData = $s->toJSON();
    		$speakerData['photo_url'] = $photo_url;
    		$speakers[] = $speakerData;

    	}

    	$comments = array();

    	foreach ($p->Comments() as $c) {
    		$comment = $c->toJSON();
    		$comment['name'] = $c->Commenter()->FirstName . ' ' . $c->Commenter()->Surname;
    		$comments[] = $comment;
    	}

    	$p->Description = str_replace(array("\r", "\n"), "", $p->Description);

        $data = $p->toJSON();        
        $data['speakers'] = $speakers;
        $data['total_votes'] = $p->Votes()->count();
        $data['average_vote'] = (int) $p->Votes()->avg('Vote');
        $data['creator'] = $p->Creator()->getName();
        $data['user_vote'] = $p->getUserVote() ? $p->getUserVote()->Vote : null;
        $data['comments'] = $comments ? $comments : null;
        $data['can_assign'] = $p->canAssign(1) ? $p->canAssign(1) : null;
        $data['selected'] = $p->isSelected();
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

	public function handleAddComment(SS_HTTPRequest $r) {
		if(!Member::currentUser()) {
			return $this->httpError(403);
		}
		
		$comment = $r->postVar('comment');

		if($comment != NULL) {
			$this->presentation->addComment($comment, Member::currentUserID());
			return new SS_HTTPResponse(null, 200);
		}

		return $this->httpError(400, "Invalid comment");
	}

	public function handleSelect(SS_HTTPResponse $r) {
		if(!Member::currentUser()) {
			return $this->httpError(403);
		}

		$this->presentation->assignToIndividualList();

	}

	public function handleUnselect(SS_HTTPResponse $r) {
		if(!Member::currentUser()) {
			return $this->httpError(403);
		}

		$this->presentation->removeFromIndividualList();

		return new SS_HTTPResponse("Presentation unseleted.", 200);

	}

	public function handleCategoryChangeRequest(SS_HTTPResponse $r) {

		if(!Member::currentUser()) {
			return $this->httpError(403);
		}

		if(!is_numeric($r->getVar('new_cat'))) return $this->httpError(500, "Invalid category id");

		$request = new SummitCategoryChange();
		$request->PresentationID = $this->presentation->ID;
		$request->NewCategoryID = $r->getVar('new_cat');
		$request->ReqesterID = Member::currentUserID();
		$request->write();

    	return new SS_HTTPResponse("change request made.", 200);		

	}


	public function handleAcceptCategoryChange(SS_HTTPResponse $r) {

		if(!Member::currentUser()) {
			return $this->httpError(403);
		}

		if(!is_numeric($r->getVar('change_id'))) return $this->httpError(500, "Invalid category change id");

		$request = SummitCategoryChange::get()->byID($r->getVar('change_id'));

		if($request->exists()) {

			// Make the category change
			$summit = Summit::get_active();
			$category = $summit->Categories()->filter('ID', $request->NewCategoryID)->first();
			if(!$category->exists()) return $this->httpError(500, "Category not found in current summit");

			$this->presentation->CategoryID = $request->NewCategoryID;
			$this->presentation->write();
			
			$comment = new SummitPresentationComment();
			$comment->Body = 'This presentaiton was moved into the category '
				. $category->Title . '.'
				. ' The chage was approved by '
				. Member::currentUser()->FirstName . ' ' . Member::currentUser()->Surname . '.';
			$comment->PresentationID = $this->presentation->ID;
			$comment->write();

			$request->ApproverID = Member::currentUserID();
			$request->Approved = TRUE;
			$request->Done = TRUE;
			$request->ApprovalDate =  SS_Datetime::now();	

			$request->write();			

    		return new SS_HTTPResponse("change request accepted.", 200);		


		}
	

	}


}