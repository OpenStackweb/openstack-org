<?php

/**
 * Class TrackChairAPI
 */
class TrackChairAPI extends AbstractRestfulJsonApi
{

    /**
     * @var array
     */
    private static $url_handlers = [
        'summit/$ID' => 'handleSummit',
        'presentation/$ID' => 'handleManagePresentation',
        'GET ' => 'handleGetAllPresentations',
        'GET selections/$categoryID' => 'handleGetMemberSelections',
        'PUT reorder' => 'handleReorderList',
        'GET changerequests' => 'handleChangeRequests',
        'POST chair/add' => 'handleAddChair',
        'DELETE chair/destroy' => 'handleDeleteChair',
        'PUT categorychange/resolve/$ID' => 'handleResolveCategoryChange',
        'GET export/chairs' => 'handleChairExport',
        'GET restoreorders' => 'handleRestoreOrders',
        'GET presentationcomments' => 'handlePresentationsWithComments',
        'GET findmember' => 'handleFindMember'
    ];

    /**
     * @var array
     */
    private static $allowed_actions = [
        'handleSummit',
        'handleManagePresentation',
        'handleGetAllPresentations',
        'handleGetMemberSelections',
        'handleReorderList',
        'handleChangeRequests',
        'handleAddChair' => 'ADMIN',
        'handleDeleteChair' => 'ADMIN',
        'handleResolveCategoryChange' => 'ADMIN',
        'handleRestoreOrders' => 'ADMIN',
        'handleChairExport' => 'ADMIN',
        'handlePresentationsWithComments' => 'ADMIN',
        'handleFindMember' => 'ADMIN'
    ];

    /**
     * @var array
     */
    private static $extensions = [
        'MemberTokenAuthenticator'
    ];

    /**
     *
     */
    public function init()
    {
        parent::init();
        $this->checkAuthenticationToken(false);
    }

    /**
     * @return bool
     */
    protected function isApiCall()
    {
        return true;
    }

    /**
     * @return bool
     */
    protected function authorize()
    {
        return !!Member::currentUser();
    }


    /**
     * @return array|void
     * @throws SS_HTTPResponse_Exception
     */
    protected function trackChairDetails()
    {

        if (!Member::currentUser()) {
            return $this->httpError(403);
        }

        $data = [
            'categories' => null,
        ];

        $summit = Summit::get_active();

        $chair = SummitTrackChair::get()
                        ->filter('MemberID', Member::currentUserID())
                        ->first();

        if ($chair) {
            $data['first_name'] = $chair->Member()->FirstName;
            $data['last_name'] = $chair->Member()->Surname;

            foreach ($chair->Categories()->filter('SummitID', $summit->ID) as $c) {
                $data['categories'][] = $c->toJSON();
            }
        }

        if (Permission::check('ADMIN')) {
            $data['is_admin'] = true;
            $data['categories'] = [];
            foreach ($summit->Categories()->filter('ChairVisible', true) as $c) {
                $data['categories'][] = $c->toJSON();
            }
        } else {
            $data['is_admin'] = false;
        }

        return $data;

    }

    /**
     * @param SS_HTTPRequest $r
     * @return SS_HTTPResponse|void
     * @throws SS_HTTPResponse_Exception
     */
    public function handleSummit(SS_HTTPRequest $r)
    {
        if ($r->param('ID') == "active") {
            $summit = Summit::get_active();
        } else {
            $summit = Summit::get()->byID($r->param('ID'));
        }

        if (!$summit) {
            return $this->httpError(404, "That summit could not be found.");
        }
        if (!$summit->exists()) {
            return $this->httpError(404, "There is no active summit.");
        }

        $data = $summit->toJSON();
        $data['status'] = $summit->getStatus();
        $data['on_voting_period'] = $summit->isVotingOpen();
        $data['on_selection_period'] = $summit->isSelectionOpen();
        $data['is_selection_period_over'] = $summit->isSelectionOver();

        $data['categories'] = [];
        $data['track_chair'] = $this->trackChairDetails();

        $chairlist = [];
        $categoriesIsChair = [];
        $categoriesNotChair = [];

        foreach ($summit->Categories()->filter('ChairVisible', true) as $c) {
            $isChair = ($c->isTrackChair(Member::currentUserID()) === 1);
            $categoryDetials = [
                'id' => $c->ID,
                'title' => $c->Title,
                'description' => $c->Description,
                'session_count' => $c->SessionCount,
                'alternate_count' => $c->AlternateCount,
                'summit_id' => $c->SummitID,
                'user_is_chair' => $isChair
            ];

            if ($isChair) {
                $categoriesIsChair[] = $categoryDetials;
            } else {
                $categoriesNotChair[] = $categoryDetials;
            }

            $chairs = $c->TrackChairs();
            foreach ($chairs as $chair) {
                $chairdata = [];
                $chairdata['chair_id'] = $chair->ID;
                $chairdata['first_name'] = $chair->Member()->FirstName;
                $chairdata['last_name'] = $chair->Member()->Surname;
                $chairdata['email'] = $chair->Member()->Email;
                $chairdata['category'] = $c->Title;
                $chairdata['category_id'] = $c->ID;
                $chairlist[] = $chairdata;
            }
        }

        $data['categories'] = array_merge($categoriesIsChair, $categoriesNotChair);

        $data['chair_list'] = $chairlist;

        return $this->ok($data);
    }

    /**
     * @param SS_HTTPRequest $r
     * @return SS_HTTPResponse
     */
    public function handleGetAllPresentations(SS_HTTPRequest $r)
    {
        // Gets a list of presentations that have chair comments
        $page_size = $r->getVar('page_size') ?: $this->config()->default_page_size;
        $page = $r->getVar('page') ?: 1;
        $summitID = Summit::get_active()->ID;

        // Get a collection of chair-visible presentation categories
        $presentations = Presentation::get()
            ->filter([
                'Category.ChairVisible' => true,
                'SummitEvent.SummitID' => $summitID,
                'Presentation.Status' => Presentation::STATUS_RECEIVED
            ]);
        
        if ($r->getVar('category')) {
            $presentations = $presentations->filter('CategoryID', (int) $r->getVar('category'));
        }
        if ($r->getVar('keyword')) {            
            $presentations = Presentation::apply_search_query($presentations, $r->getVar('keyword'));
        }

        $offset = ($page - 1) * $page_size;
        $count = $presentations->count();
        $presentations = $presentations->limit($page_size, $offset);

        $data = [
            'results' => [],
            'page' => $page,
            'total_pages' => ceil($count / $page_size),
            'results' => [],
            'has_more' => $count > ($page_size * ($page)),
            'total' => $count,
            'remaining' => $count - ($page_size * ($page))
        ];

        foreach ($presentations as $p) {
            $data['results'][] = [
                'id' => $p->ID,
                'title' => $p->Title,
                'viewed' => $p->isViewedByTrackChair(),
                'selected' => $p->getSelectionType(),
                'selectors' => array_keys($p->getSelectors()->map('Name','Name')->toArray()),
                'likers' => array_keys($p->getLikers()->map('Name','Name')->toArray()),
                'passers' => array_keys($p->getPassers()->map('Name','Name')->toArray()),
                'comment_count' => $p->Comments()->count(),
                'popularity' => $p->getPopularityScore(),
                'vote_count' => $p->CalcVoteCount(),
                'vote_average' => $p->CalcVoteAverage(),
                'total_points' => $p->CalcTotalPoints(),
                'moved_to_category' => $p->movedToThisCategory(),
                'speakers' => $p->getSpeakersCSV()
            ];
        }

        return $this->ok($data);
    }

    /**
     * @param SS_HTTPRequest $r
     * @throws SS_HTTPResponse_Exception
     */
    public function handleManagePresentation(SS_HTTPRequest $r)
    {

        /* if(!Member::currentUser()) {
            return $this->httpError(403);
        } */

        if ($presentation = Presentation::get()->byID($r->param('ID'))) {
            $request = TrackChairAPI_PresentationRequest::create($presentation, $this);

            return $request->handleRequest($r, DataModel::inst());
        }

        return $this->httpError(404);
    }

    /**
     * @param SS_HTTPRequest $r
     * @return SS_HTTPResponse
     */
    public function handleGetMemberSelections(SS_HTTPRequest $r)
    {
        $results = [];
        $categoryID = (int)$r->param('categoryID');
        $results['category_id'] = $categoryID;

        $category = PresentationCategory::get()->byID($categoryID);
        if (!$category) {
            return $this->notFound(sprintf('Category id %s not found!', $categoryID));
        }
        $summitID = (int) Summit::get_active()->ID;
        if (intval($category->SummitID) !== $summitID) {
            return $this->validationError(sprintf('Category id %s does not belong to current summit!', $categoryID));
        }

        $lists = SummitSelectedPresentationList::getAllListsByCategory($categoryID);

        foreach ($lists as $key => $list) {
            $selections = $list->SummitSelectedPresentations()
            					->exclude('Collection', SummitSelectedPresentation::COLLECTION_PASS)
            					->sort(['Collection DESC', 'Order ASC']);
            
            $count = $selections->count();
            $listID = $list->ID;

            $data = [
            	'id' => $listID,
                'list_name' => $list->name,
                'list_type' => $list->ListType,
                'list_id' => $listID,
                'total' => $count,
                'can_edit' => $list->memberCanEdit(),
                'slots' => $list->maxPresentations(),
                'alternates' => $list->Category()->AlternateCount,
                'mine' => $list->mine(),
                'selections' => [],
                'maybes' => []                
            ];

            foreach ($selections as $s) {
            	$p = $s->Presentation();
            	$selectionData = [
                    'presentation' => [
                    	'title' => $p->Title,
		                'selectors' => array_keys($p->getSelectors()->map('Name','Name')->toArray()),
		                'likers' => array_keys($p->getLikers()->map('Name','Name')->toArray()),
		                'passers' => array_keys($p->getPassers()->map('Name','Name')->toArray()),
		                'group_selected' => $p->isGroupSelected(),
		                'comment_count' => $p->Comments()->count(),
		                'level' => $p->Level
		            ],
                    'order' => $s->Order,
                    'id' => $s->PresentationID
                ];
                if($s->isSelected()) {
                	$data['selections'][] = $selectionData;
                }
                else if($s->isMaybe()) {
                	$data['maybes'][] = $selectionData;
                }                
            }

            $results['lists'][] = $data;

        }

        return $this->ok($results);
    }

    /**
     * @param SS_HTTPRequest $r
     * @return SS_HTTPResponse
     * @throws ValidationException
     * @throws null
     */
    public function handleReorderList(SS_HTTPRequest $r)
    {        
        $vars = Convert::json2array($r->getBody());        
        $idList = $vars['order'];
        $listID = $vars['list_id'];
        $collection = $vars['collection'];        	
        $list = SummitSelectedPresentationList::get()->byId($listID);
        
        if(!$list->memberCanEdit()) {
        	return $this->httpError(403, 'You cannot edit this list');
        }
        
        $isTeam = $list->ListType === 'Group';

        // Remove any presentations that are not in the list
        SummitSelectedPresentation::get()
        	->filter([
        		'SummitSelectedPresentationListID' => $listID,
        		'Collection' => $collection
        	])
        	->exclude([
        		'PresentationID' => array_values($idList)
        	])
        	->removeAll();

        if (is_array($idList)) {
            foreach ($idList as $order => $id) {
            	$attributes = [
                    'PresentationID' => $id,
                    'SummitSelectedPresentationListID' => $listID,
                    'Collection' => $collection
                ];

                $selection = SummitSelectedPresentation::get()
                	->filter($attributes)
                	->first();

                if(!$selection) {
                	$selection = SummitSelectedPresentation::create($attributes);                	
                	if($isTeam) {
                		$presentation = Presentation::get()->byID($id);
                		if($presentation) {
                			$presentation->addNotification('{member} added this presentation to the team list');
                		}
                	}
                }

                $selection->Order = $order+1;
                $selection->write();
            }
        }

        return $this->ok();

    }

    /**
     * @param SS_HTTPRequest $r
     * @return SS_HTTPResponse
     */
    public function handleChangeRequests(SS_HTTPRequest $r)
    {
        $summit = Summit::get_active();
        $summitID = $summit->ID;

        $page_size = $r->getVar('page_size') ?: $this->config()->default_page_size;
        $page = $r->getVar('page') ?: 1;

        $track_chair = SummitTrackChair::get()->filter([
            'MemberID' => Member::currentUserID()
        ])->first();
        $categories = [];

        if($track_chair)
            $categories = $track_chair->Categories()
                ->filterByCallback(function($category) use ($summit) {
                    return $summit->isPublicCategory($category);
                })
                ->column('ID');

        $changeRequests = SummitCategoryChange::get()
            ->innerJoin('Presentation', 'Presentation.ID = PresentationID')
            ->innerJoin('SummitEvent', 'Presentation.ID = SummitEvent.ID')
            ->leftJoin('PresentationCategory', 'OldCategory.ID = Presentation.CategoryID','OldCategory')
            ->leftJoin('PresentationCategory', 'NewCategory.ID = SummitCategoryChange.NewCategoryID','NewCategory')
            ->leftJoin('Member', 'Member.ID = SummitCategoryChange.ReqesterID')
            ->filter([
            	'SummitEvent.SummitID' => $summitID            	
            ]);

        if(!Permission::check('ADMIN')) {
        	$changeRequests = $changeRequests->filter([
        		'Presentation.CategoryID' => $categories
        	]);
        }

        $sortCol = $r->getVar('sortCol') ?: 'status';
        $sortDir = $r->getVar('sortDir') == 1 ? 'ASC' : 'DESC';
        $search = $r->getVar('search');

        switch($sortCol) {
        	case 'presentation':
        		$sortClause = "SummitEvent.Title";
        		break;
        	case 'status':
        		$sortClause = "Status";
        		break;
        	case 'oldcat':
        		$sortClause = "OldCategory.Title";
        		break;
        	case 'newcat':
        		$sortClause = "NewCategory.Title";
        		break;
        	case 'requester':
        		$sortClause = "Member.Surname";
        		break;
        	default:
        		$sortClause = "Done";
        }

        $changeRequests = $changeRequests->sort($sortClause, $sortDir);

        if($search) {
        	$changeRequests = $changeRequests->filterAny([
        		'SummitEvent.Title:PartialMatch' => $search,
        		'OldCategory.Title' => $search,
        		'NewCategory.Title' => $search,
        		'Member.Surname' => $search
        	]);
        }

        $isAdmin = Permission::check('ADMIN');
        $memID = Member::currentUserID();

        $offset = ($page - 1) * $page_size;
        $count = $changeRequests->count();
        $changeRequests = $changeRequests->limit($page_size, $offset);

        $data = [
            'results' => [],
            'page' => $page,
            'total_pages' => ceil($count / $page_size),
            'results' => [],
            'has_more' => $count > ($page_size * ($page)),
            'total' => $count,
            'remaining' => $count - ($page_size * ($page))
        ];

        foreach ($changeRequests as $request) {
            $row = [];
            $row['id'] = $request->ID;
            $row['presentation_id'] = $request->PresentationID;
            $row['presentation_title'] = $request->Presentation()->Title;
            $row['is_admin'] = $isAdmin;
            $row['status'] = $request->getNiceStatus();
            $row['chair_of_old'] = $request->Presentation()->Category()->isTrackChair($memID);
            $row['chair_of_new'] = $request->NewCategory()->isTrackChair($memID);
            $row['new_category']['title'] = $request->NewCategory()->Title;
            $row['new_category']['id'] = $request->NewCategory()->ID;
            $row['old_category']['title'] = $request->Presentation()->Category()->Title;
            $row['old_category']['id'] = $request->Presentation()->Category()->ID;
            $row['requester'] = $request->Reqester()->FirstName
                . ' ' . $request->Reqester()->Surname;
            $row['has_selections'] = $request->Presentation()->isSelectedByAnyone();
            $data['results'][] = $row;
        }

        return $this->ok($data);
    }

    /**
     * @param SS_HTTPRequest $r
     * @return string
     */
    public function handleAddChair(SS_HTTPRequest $r)
    {    	
        $email = $r->postVar('email');
        $catid = $r->postVar('category');
        $category = PresentationCategory::get()->byID($catid);
        if (!$category) {
            return $this->httpError(404, 'Category not found');
        }

        $member = Member::get()->filter('Email', $email)->first();
        if (!$member) {
            return $this->httpError(404, 'Member not found');
        }
        $id = SummitTrackChair::addChair($member, $catid);
        $category->MemberList($member->ID);
        $category->GroupList();

        return (new SS_HTTPResponse(Convert::array2json([
        		'chair_id' => $id,
        		'first_name' => $member->FirstName,
        		'last_name' => $member->Surname,
        		'email' => $member->Email,
        		'category' => $category->Title,
        		'category_id' => $category->ID
        	]), 200))
        	->addHeader('Content-type', 'application/json');
    }

    public function handleDeleteChair(SS_HTTPRequest $r)
    {
    	parse_str($r->getBody(), $vars);
    	
    	if(!isset($vars['chairID']) || !isset($vars['categoryID'])) {
    		return $this->httpError(400, 'You must provide a chairID and categoryID param');
    	}

    	$category = PresentationCategory::get()->byID($vars['categoryID']);
    	$chair = SummitTrackChair::get()->byID($vars['chairID']);

    	if(!$category) {
    		return $this->httpError(404, 'Category not found');
    	}

    	if(!$chair) {
    		return $this->httpError(404, 'Chair not found');
    	}

    	$category->TrackChairs()->remove($chair);

    	return new SS_HTTPResponse("Chair {$chair->Member()->getName()} removed from {$category->Title}", 200);
    }	

    /**
     * @param SS_HTTPResponse $r
     * @return SS_HTTPResponse|void
     * @throws SS_HTTPResponse_Exception
     * @throws ValidationException
     * @throws null
     */
    public function handleResolveCategoryChange(SS_HTTPResponse $r)
    {

        if (!Permission::check('ADMIN')) {
            return $this->httpError(403);
        }

        if (!is_numeric($r->param('ID'))) {
            return $this->httpError(500, "Invalid category change id");
        }

        $vars = Convert::json2array($r->getBody());        
        if(!isset($vars['approved'])) {
        	return $this->httpError(500, "Request body must contain 'approved' 1 or 0");	
        }
        $approved = (boolean) $vars['approved'];

        $request = SummitCategoryChange::get()->byID($r->param('ID'));
        if(!$request) {
        	return $this->httpError(500, "Request " . $r->param('ID') . " does not exist");
        }
        
    	$status = $approved ? SummitCategoryChange::STATUS_APPROVED : SummitCategoryChange::STATUS_REJECTED;

        if ($request->Presentation()->isSelectedByAnyone()) {
            return new SS_HTTPResponse("The presentation has already been selected by chairs.", 500);
        }

        if ($request->Presentation()->CategoryID == $request->NewCategoryID) {
            return new SS_HTTPResponse("The presentation is already in this category.", 200);
        }

        // Make the category change
        $summit = Summit::get_active();
        $category = $summit->Categories()->filter('ID', $request->NewCategoryID)->first();
        if (!$category->exists()) {
            return $this->httpError(500, "Category not found in current summit");
        }
		
        if($approved) {
	        $request->OldCategoryID = $request->Presentation()->CategoryID;
	        $request->Presentation()->CategoryID = $request->NewCategoryID;
	        $request->Presentation()->write();    
	        $request->Presentation()->addNotification(
	        	'{member} approved ' . $request->Reqester()->getName() .'\'s request to move this presentation to ' . $category->Title
	        );
    	}
    	else {	   
    		$request->Presentation()->addNotification(
    			'{member} rejected ' . $request->Reqester()->getName() .'\'s request to move this presentation to ' . $category->Title
    		);     
    	}
        
        $request->AdminApproverID = Member::currentUserID();
        $request->Status = $status;
        $request->ApprovalDate = SS_Datetime::now();
        $request->write();

        $peers = SummitCategoryChange::get()
        	->filter([
        		'PresentationID' => $request->PresentationID,
        		'NewCategoryID' => $request->NewCategoryID
        	])
        	->exclude([
        		'ID' => $request->ID
        	]);

        foreach($peers as $p) {
            $p->AdminApproverID = Member::currentUserID();
            $p->Status = SummitCategoryChange::STATUS_APPROVED;
            $p->ApprovalDate = SS_Datetime::now();
            $p->write();
        }

        return $this->ok('change request accepted.');
    }


    /**
     *
     */
    public function handleChairExport()
    {
        $activeSummit = Summit::get_active();
        $filepath = Controller::join_links(
        	BASE_PATH,
        	ASSETS_DIR,
        	'track-chairs.csv'
        );

        $fp = fopen($filepath, 'w');

        // Setup file to be UTF8
        fprintf($fp, chr(0xEF) . chr(0xBB) . chr(0xBF));

        $categories = $activeSummit->Categories()
        		->filter('ChairVisible', true);        		

        foreach($categories as $cat) {
	        foreach ($cat->TrackChairs() as $c) {
	        	$m = $c->Member();
	            $fields = [
	                $m->FirstName,
	                $m->Surname,
	                $m->Email,
	                $cat->Title
	            ];
	            fputcsv($fp, $fields);
	        }
        }

        fclose($fp);

        header("Cache-control: private");
        header("Content-type: application/force-download");
        header("Content-transfer-encoding: binary\n");
        header("Content-disposition: attachment; filename=\"track-chairs.csv\"");
        header("Content-Length: " . filesize($filepath));
        readfile($filepath);
    }


    public function handleFindMember(SS_HTTPRequest $r)
    {
    	$search = Convert::raw2sql($r->getVar('search'));
    	$results = Member::get()
    				->where(
    					"Email LIKE '%{$search}%' " .
    					"OR (CONCAT_WS(' ', FirstName, Surname)) LIKE '%{$search}%'"
    				)
    				->limit(10);    			

    	$json = [];
    	foreach($results as $member) {
    		$json[] = [
    			'id' => $member->ID,
    			'name' => $member->getName(),
    			'email' => $member->Email
    		];
    	}

        return (new SS_HTTPResponse(Convert::array2json($json), 200))
    		->addHeader('Content-type', 'application/json');
    }
}


/**
 * Class TrackChairAPI_PresentationRequest
 */
class TrackChairAPI_PresentationRequest extends RequestHandler
{

    /**
     * @var array
     */
    private static $url_handlers = [
        'GET ' => 'index',
        'POST vote' => 'handleVote',
        'POST comment' => 'handleAddComment',
        'POST emailspeakers' => 'handleEmailSpeakers',
        'PUT select' => 'handleSelect',
        'PUT unselect' => 'handleUnselect',
        'PUT markasviewed' => 'handleMarkAsViewed',
        'PUT group/select' => 'handleGroupSelect',
        'PUT group/unselect' => 'handleGroupUnselect',
        'POST categorychange/new' => 'handleCategoryChangeRequest',
    ];


    /**
     * @var array
     */
    private static $allowed_actions = [
        'handleVote',
        'handleAddComment',
        'handleEmailSpeakers',
        'handleSelect',
        'handleUnselect',
        'handleMarkAsViewed',
        'handleCategoryChangeRequest',
        'handleGroupSelect',
        'handleGroupUnselect'
    ];


    /**
     * @var Presentation
     */
    protected $presentation;


    /**
     * @var PresentationAPI
     */
    protected $parent;

    /**
     * TrackChairAPI_PresentationRequest constructor.
     * @param Presentation $presentation
     * @param PresentationAPI $parent
     */
    public function __construct(Presentation $presentation, PresentationAPI $parent)
    {
        parent::__construct();
        $this->presentation = $presentation;
        $this->parent = $parent;
    }

    /**
     * @param SS_HTTPRequest $r
     * @return SS_HTTPRequest
     */
    public function index(SS_HTTPRequest $r)
    {
        $p = $this->presentation;
        $speakers = [];
        $current_summit = $p->Summit();

        foreach ($p->Speakers() as $s) {
            // if($s->Bio == NULL) $s->Bio = "&nbsp;";
            $s->Bio = str_replace(array("\r", "\n"), "", $s->Bio);
            $speakerData = $s->toJSON();
            $speakerData['photo_url'] = $s->ProfilePhoto();
            $speakerData['available_for_bureau'] = intval($speakerData['available_for_bureau']);

            $expertise_areas = [];
            foreach ($s->AreasOfExpertise() as $a) {
                array_push($expertise_areas, [
                    'id' => $a->ID,
                    'expertise' => $a->Expertise
                ]);
            }
            $speakerData['expertise_areas'] = $expertise_areas;

            $former_presentations = [];
            $formerList = $s->Presentations()
                            ->exclude('SummitID', $current_summit->ID)
                            ->limit(5)
                            ->sort('StartDate', 'DESC');
            foreach ($formerList as $pf) {
                array_push($former_presentations, [
                    'id' => $pf->ID,
                    'title' => $pf->Title,
                    'url' => $pf->Link
                ]);
            }
            $speakerData['former_presentations'] = $former_presentations;

            $links = [];
            foreach ($s->OtherPresentationLinks() as $l) {
                array_push($links, [
                    'id' => $l->ID,
                    'title' => $l->Title,
                    'url' => $l->LinkUrl
                ]);
            }
            $speakerData['other_links'] = $links;

            $travel_preferences = [];
            foreach ($s->TravelPreferences() as $t) {
                array_push($travel_preferences, [
                    'id' => $t->ID,
                    'country' => $t->Country
                ]);
            }
            $speakerData['travel_preferences'] = $travel_preferences;
            $languages = [];
            foreach ($s->Languages() as $l) {
                array_push($languages, [
                    'id' => $l->ID,
                    'language' => $l->Language
                ]);
            }
            $speakerData['languages'] = $languages;
            $speakers[] = $speakerData;
        }

        $comments = [];

        foreach ($p->Comments() as $c) {
            $comment = $c->toJSON();
            $comment['name'] = $c->Commenter()->FirstName . ' ' . $c->Commenter()->Surname;
            $comment['ago'] =  $c->obj('Created')->Ago(false);
            $comment['is_activity'] = (boolean) $c->IsActivity;
            $comments[] = $comment;
        }

        // remove unsafe character for JSON
        $p->ShortDescription = ($p->ShortDescription != null) ? str_replace(array("\r", "\n"), "",
            $p->ShortDescription) : '(no description provided)';
        $p->ProblemAddressed = ($p->ProblemAddressed != null) ? str_replace(array("\r", "\n"), "",
            $p->ProblemAddressed) : '(no answer provided)';
        $p->AttendeesExpectedLearnt = ($p->AttendeesExpectedLearnt != null) ? str_replace(array("\r", "\n"), "",
            $p->AttendeesExpectedLearnt) : '(no answer provided)';

        $data = $p->toJSON();
        $data['title'] = $p->Title;
        $data['description'] = ($p->ShortDescription != null) ? $p->ShortDescription : '(no description provided)';
        $data['category_name'] = $p->Category()->Title;
        $data['speakers'] = $speakers;
        $data['total_votes'] = $p->Votes()->count();
        $data['vote_count'] = $p->CalcVoteCount();
        $data['vote_average'] = $p->CalcVoteAverage();
        $data['total_points'] = ($p->CalcTotalPoints() > 0) ? $p->CalcTotalPoints() : '0';
        $data['creator'] = $p->Creator()->getName();
        $data['user_vote'] = $p->getUserVote() ? $p->getUserVote()->Vote : null;
        $data['comments'] = $comments;
        $data['can_assign'] = $p->canAssign(1) ? $p->canAssign(1) : null;
        $data['selected'] = $p->getSelectionType();
        $data['selectors'] = array_keys($p->getSelectors()->map('Name','Name')->toArray());
        $data['likers'] = array_keys($p->getLikers()->map('Name','Name')->toArray());
        $data['passers'] = array_keys($p->getPassers()->map('Name','Name')->toArray());
        $data['popularity'] = $p->getPopularityScore();
        $data['group_selected'] = $p->isGroupSelected();
        $data['moved_to_category'] = $p->movedToThisCategory();
        $data['change_requests_count'] = SummitCategoryChange::get()->filter([
        		'PresentationID' => $p->ID,
        		'Status' => SummitCategoryChange::STATUS_PENDING
        	])->count();

        return (new SS_HTTPResponse(
            Convert::array2json($data), 200
        ))->addHeader('Content-Type', 'application/json');

    }

    /**
     * @param SS_HTTPRequest $r
     * @return SS_HTTPResponse|void
     * @throws SS_HTTPResponse_Exception
     */
    public function handleAddComment(SS_HTTPRequest $r)
    {
        if (!Member::currentUser()) {
            return $this->httpError(403);
        }

        $comment = $r->postVar('comment');

        if ($comment != null) {
            $commentObj = $this->presentation->addComment($comment, Member::currentUserID());
            
            $json = $commentObj->toJSON();
            $json['name'] = Member::currentUser()->getName();
            $json['ago'] = $commentObj->obj('Created')->Ago(false);

	        return (new SS_HTTPResponse(
	            Convert::array2json($json), 200
	        ))->addHeader('Content-Type', 'application/json');

        }

        return $this->httpError(400, "Invalid comment");
    }

    /**
     * @param SS_HTTPRequest $r
     * @return SS_HTTPResponse|void
     * @throws SS_HTTPResponse_Exception
     */
    public function handleEmailSpeakers(SS_HTTPRequest $r)
    {
        if (!Member::currentUser()) {
            return $this->httpError(403);
        }

        $email = $r->postVar('email');

        if ($email != null) {
            $current_user = Member::currentUser();
            $addresses = [];
            foreach($this->presentation->Speakers() as $s) {
            	$addresses[] = $s->getEmail();
            }
            $subject = "Track chair {$current_user->getName()} has a question about your presentation";
            $body = $email;
            $email = EmailFactory::getInstance()->buildEmail(
            	null,
            	implode(',',$addresses),
            	$subject,
            	$body
            );
            $email->setCc('speakersupport@openstack.org');
            
            try {
            	$email->send();
	        	$this->presentation->addNotification('
	        		{member} emailed the speakers
	        	');        	

            	return new SS_HTTPResponse('OK');
        	} catch(Exception $e) {
        		return new SS_HTTPResponse($e->getMessage(), 400);
        	}

        }

        return $this->httpError(400, "Invalid comment");
    }

    /**
     * @param SS_HTTPResponse $r
     * @throws SS_HTTPResponse_Exception
     */
    public function handleSelect(SS_HTTPResponse $r)
    {
        if (!Member::currentUser()) {
            return $this->httpError(403);
        }

        $maybe = SummitSelectedPresentation::COLLECTION_MAYBE;
        $pass = SummitSelectedPresentation::COLLECTION_PASS;
        $selected = SummitSelectedPresentation::COLLECTION_SELECTED;
        
        switch($r->getVar('type')) {
        	case $maybe:
        		$this->presentation->assignToIndividualList($maybe);
        		break;
        	case $pass:
        		$this->presentation->assignToIndividualList($pass);
        		break;
        	default:
        		$this->presentation->assignToIndividualList($selected);
        		break;
        }
        
        return new SS_HTTPResponse('OK');
    }

    /**
     * @param SS_HTTPResponse $r
     * @return SS_HTTPResponse|void
     * @throws SS_HTTPResponse_Exception
     */
    public function handleUnselect(SS_HTTPResponse $r)
    {
        if (!Member::currentUser()) {
            return $this->httpError(403);
        }

        $this->presentation->removeFromIndividualList();

        return new SS_HTTPResponse("Presentation unselected.", 200);

    }

    public function handleMarkAsViewed(SS_HTTPResponse $r) 
    {
    	try {
    		$this->presentation->markAsViewedByTrackChair();
    		return new SS_HTTPResponse('Marked as viewed');
    	} catch(Exception $e) {
    		return new SS_HTTPResponse("Error", 500);
    	}
    }

    /**
     * @param SS_HTTPResponse $r
     * @throws SS_HTTPResponse_Exception
     */
    public function handleGroupSelect(SS_HTTPResponse $r)
    {
        if (!Member::currentUser()) {
            return $this->httpError(403);
        }

        $this->presentation->assignToGroupList();

        return new SS_HTTPResponse('OK');

    }

    /**
     * @param SS_HTTPResponse $r
     * @return SS_HTTPResponse|void
     * @throws SS_HTTPResponse_Exception
     */
    public function handleGroupUnselect(SS_HTTPResponse $r)
    {
        if (!Member::currentUser()) {
            return $this->httpError(403);
        }

        $this->presentation->removeFromGroupList();

        return new SS_HTTPResponse("Presentation unselected.", 200);

    }

    /**
     * @param SS_HTTPResponse $r
     * @return SS_HTTPResponse|void
     * @throws SS_HTTPResponse_Exception
     * @throws ValidationException
     * @throws null
     */
    public function handleCategoryChangeRequest(SS_HTTPResponse $r)
    {

        if (!Member::currentUser()) {
            return $this->httpError(403);
        }
        $newCat = $r->postVar('new_cat');
        if (!is_numeric($newCat)) {
            return $this->httpError(500, "Invalid category id");        
        }

        $c = PresentationCategory::get()->byID($newCat);

        if ($c) {
            $request = SummitCategoryChange::create();
            $request->PresentationID = $this->presentation->ID;
            $request->NewCategoryID = $newCat;
            $request->ReqesterID = Member::currentUserID();
            $request->write();

            $this->presentation->addNotification('
            	{member} submitted a request to change the category to '.$c->Title
            );           
            
            return new SS_HTTPResponse("change request made.", 200);

        }
    }
}
