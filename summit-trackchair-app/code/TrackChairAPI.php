<?php

/**
 * Class TrackChairAPI
 */
class TrackChairAPI extends AbstractRestfulJsonApi
{
    use RestfulJsonApiResponses;
    /**
     * @var array
     */
    private static $url_handlers = [
        'summit/$ID' => 'handleSummit',
        'presentation/$ID' => 'handleManagePresentation',
        'GET ' => 'handleGetAllPresentations',
        'GET selections/$categoryID/$listClass' => 'handleGetMemberSelections',
        'PUT reorder' => 'handleReorderList',
        'GET changerequests' => 'handleChangeRequests',
        'POST chair/add' => 'handleAddChair',
        'DELETE chair/destroy' => 'handleDeleteChair',
        'PUT categorychange/resolve/$ID' => 'handleResolveCategoryChange',
        'GET export/chairs' => 'handleChairExport',
        'GET export/presentations' => 'handlePresentationsExport',
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
        'handleResolveCategoryChange',
        'handlePresentationsExport',
        'handleAddChair' => 'ADMIN',
        'handleDeleteChair' => 'ADMIN',
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

    private $tx_manager;

    /**
     *
     */
    public function init()
    {
        parent::init();
        $this->checkAuthenticationToken(false);
        $this->tx_manager = SapphireTransactionManager::getInstance();
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

        $data['categories'] = [];
        $data['track_chair'] = $this->trackChairDetails();

        $chairlist = [];
        $categoriesIsChair = [];
        $categoriesNotChair = [];
        $selectionPlan = $summit->getOpenSelectionPlanForStage('Selection');

        if ($selectionPlan) {
            foreach ($selectionPlan->getSelectionCategories() as $c) {
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
        }

        $data['categories'] = array_merge($categoriesIsChair, $categoriesNotChair);

        $data['chair_list'] = $chairlist;

        $data['list_classes'] = array(
            ['id'=>SummitSelectedPresentationList::Session, 'title' => 'Presentations']
        );

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
        $count = 0;
        $types = [];
        $cloud_data = [];
        $presentations = [];
        $summit = Summit::get_active();
        $summitID = $summit->ID;

        if ($summit->isSelectionOpen()) {
            // Get a collection of chair-visible presentation categories
            $presentations = Presentation::get()
                ->filter([
                    'Category.ChairVisible' => true,
                    'SummitEvent.SummitID' => $summitID,
                    'Presentation.Status' => Presentation::STATUS_RECEIVED
                ]);

            if ($r->getVar('category')) {
                $presentations = $presentations->filter('CategoryID', (int)$r->getVar('category'));
            }
            if ($keyword = $r->getVar('keyword')) {
                if (strpos($keyword, 'Tag:') === 0) {
                    $tag = substr($keyword, 4);
                    $presentations = $presentations->leftJoin(
                        "SummitEvent_Tags",
                        "Presentation.ID = SummitEvent_Tags.SummitEventID"
                    )
                        ->leftJoin(
                            "Tag",
                            "Tag.ID = SummitEvent_Tags.TagID"
                        )->where("Tag.Tag = '{$tag}' ");
                } else {
                    $presentations = Presentation::apply_search_query($presentations, $keyword);
                }
            }

            foreach ($presentations as $p) {
                if (array_search($p->Type()->ID, array_column($types, 'id')) === false)
                    $types[] = ['id' => $p->Type()->ID, 'type' => $p->Type()->Type];
            }

            foreach ($presentations as $pres) {
                foreach ($pres->getWordCloud() as $word => $count) {
                    $key = array_search($word, array_column($cloud_data, 'value'));
                    if ($key === false) {
                        $cloud_data[] = ['value' => $word, 'count' => $count];
                    } else {
                        $cloud_data[$key]['count'] += $count;
                    }
                }
            }

            $cloud_data = array_values(array_filter($cloud_data, function ($v) {
                return $v['count'] > 1;
            }));

            $offset = ($page - 1) * $page_size;
            $count = $presentations->count();
            $presentations = $presentations->limit($page_size, $offset);
        }

        $data = [
            'results' => [],
            'page' => $page,
            'total_pages' => ceil($count / $page_size),
            'has_more' => $count > ($page_size * ($page)),
            'total' => $count,
            'remaining' => $count - ($page_size * ($page)),
            'types' => $types,
            'cloud_data' => $cloud_data
        ];

        foreach ($presentations as $p) {
            $is_group_selected = $p->isGroupSelected();

            $data['results'][] = [
                'id' => $p->ID,
                'title' => $p->Title,
                'viewed' => $p->isViewedByTrackChair(),
                'group_selected' => $is_group_selected,
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
                'speakers' => $p->getSpeakersCSV(),
                'type' => $p->Type()->Type
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
        $list_class = $r->param('listClass');
        $results['category_id'] = $categoryID;
        $results['lists'] = array();

        $category = PresentationCategory::get()->byID($categoryID);
        if (!$category) {
            return $this->validationError(sprintf('Category id %s not found!', $categoryID));
        }

        $results['accepted_count'] = $category->SessionCount;
        $results['alternate_count'] = $category->AlternateCount;

        $summitID = (int) Summit::get_active()->ID;
        if (intval($category->SummitID) !== $summitID) {
            return $this->validationError(sprintf('Category id %s does not belong to current summit!', $categoryID));
        }

        if ($list_class) {
            $lists = SummitSelectedPresentationList::getAllListsByCategory($categoryID, $list_class);
        } else {
            $lists = SummitSelectedPresentationList::getAllListsByCategory($categoryID, SummitSelectedPresentationList::Session);
        }

        foreach ($lists as $key => $list) {
            $selections = $list->SummitSelectedPresentations()
            					->exclude('Collection', SummitSelectedPresentation::COLLECTION_PASS)
            					->sort(['Collection DESC', 'Order ASC']);
            
            $count = intval($selections->count());
            $listID = $list->ID;

            $data = [
            	'id' => $listID,
                'list_id' => $listID,
                'list_name' => $list->name,
                'list_type' => $list->ListType,
                'list_class' => $list->ListClass,
                'is_group' => $list->isGroup(),
                'list_hash' => $list->Hash,
                'member_id' => $list->Member()->ID,
                'total' => $count,
                'can_edit' => $list->memberCanEdit(),
                'slots' => $list->maxPresentations(),
                'alternates' => $list->maxAlternates(),
                'mine' => $list->mine(),
                'selections' => [],
                'maybes' => []                
            ];

            foreach ($selections as $s) {
            	$p = $s->Presentation();
                $is_group_selected = $p->isGroupSelected();

            	$selectionData = [
                    'presentation' => [
                    	'title' => $p->Title,
		                'selectors' => array_keys($p->getSelectors()->map('Name','Name')->toArray()),
		                'likers' => array_keys($p->getLikers()->map('Name','Name')->toArray()),
		                'passers' => array_keys($p->getPassers()->map('Name','Name')->toArray()),
		                'group_selected' => $is_group_selected,
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
        try {
            $vars = Convert::json2array($r->getBody());
            $idList = $vars['order'];
            $old_hash = isset($vars['list_hash']) ? $vars['list_hash'] : null;
            $new_hash = $old_hash;
            $listID = $vars['list_id'];
            $collection = $vars['collection'];
            $list = SummitSelectedPresentationList::get()->byId($listID);
            $max_limit = $list->maxPresentations();

            if(!$list->memberCanEdit()) {
                return $this->validationError('You cannot edit this list');
            }

            if (count($idList) > $max_limit) {
                $msg = "Can't add this presentation, limit is ".$max_limit;
                throw new EntityValidationException($msg);
            }

            if (is_array($idList)) {
                $new_hash = $this->tx_manager->transaction(function() use ($idList, $collection, $list, $old_hash){

                    $isTeam = ($list->isGroup());

                    // first we compare the list hash to see if there were modifications
                    // we only do this for team bc there are 2 individual lists maybe and selections

                    if ($isTeam && !$list->compareHashString($old_hash)) {
                        $msg = "The list was modified by someone else, please REFRESH";
                        throw new EntityValidationException($msg);
                    }

                    // Remove any presentations that are not in the list
                    // for team selection we remove all, for individual we just remove the one not there, just to save time
                    $current_presentations = SummitSelectedPresentation::get()
                                                    ->filter([
                                                        'SummitSelectedPresentationListID' => $list->ID,
                                                        'Collection' => $collection
                                                    ]);
                    if (!$isTeam) {
                        $current_presentations->exclude(['PresentationID' => array_values($idList)]);
                    }

                    $current_presentations->removeAll();

                    foreach ($idList as $order => $id) {
                        $attributes = [
                            'PresentationID' => $id,
                            'SummitSelectedPresentationListID' => $list->ID,
                            'Collection' => $collection,
                            'MemberID' => (!$isTeam) ? Member::currentUser()->ID : 0
                        ];

                        $selection = SummitSelectedPresentation::get()
                            ->filter($attributes)
                            ->first();

                        if(!$selection) {
                            $presentation = Presentation::get()->byID($id);
                            $selection = SummitSelectedPresentation::create($attributes);

                            if($isTeam && $presentation) {
                                $presentation->addNotification('{member} added this presentation to the team list');
                            }
                        }

                        $selection->Order = $order+1;
                        $selection->write();
                    }

                    $list->setHashString();
                    $list->write();

                    return $list->Hash;
                });

            }

            return $this->ok(['new_hash' => $new_hash]);
        }
        catch(EntityValidationException $ex1){
            SS_Log::log($ex1->getMessage(), SS_Log::WARN);
            return $this->validationError($ex1->getMessages());
        }
        catch(Exception $ex){
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
            return $this->serverError();
        }

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
            ->leftJoin('PresentationCategory', 'OldCategory.ID = SummitEvent.CategoryID','OldCategory')
            ->leftJoin('PresentationCategory', 'NewCategory.ID = SummitCategoryChange.NewCategoryID','NewCategory')
            ->leftJoin('Member', 'Member.ID = SummitCategoryChange.ReqesterID')
            ->filter([
            	'SummitEvent.SummitID' => $summitID            	
            ]);

        if(!Permission::check('ADMIN') && $categories) {
            $cat_ids_string = implode(',',$categories);
        	$changeRequests = $changeRequests->where(
                "SummitEvent.CategoryID IN (".$cat_ids_string.") OR SummitCategoryChange.NewCategoryID IN(".$cat_ids_string.")"
        	);
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
        		$sortClause = "Status";
        }

        $changeRequests = $changeRequests->sort($sortClause . $sortDir . ", LastEdited DESC");

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
            'has_more' => $count > ($page_size * ($page)),
            'total' => $count,
            'remaining' => $count - ($page_size * ($page))
        ];

        foreach ($changeRequests as $request) {
            $old_category = ($request->Status == SummitCategoryChange::STATUS_PENDING) ? $request->Presentation()->Category() : $request->OldCategory();

            $row = [];
            $row['id'] = $request->ID;
            $row['presentation_id'] = $request->PresentationID;
            $row['presentation_title'] = $request->Presentation()->Title;
            $row['is_admin'] = $isAdmin;
            $row['status'] = $request->getNiceStatus();
            $row['reject_reason'] = $request->Reason;
            $row['chair_of_old'] = $request->Presentation()->Category()->isTrackChair($memID);
            $row['chair_of_new'] = $request->NewCategory()->isTrackChair($memID);
            $row['new_category']['title'] = $request->NewCategory()->Title;
            $row['new_category']['id'] = $request->NewCategory()->ID;
            $row['old_category']['title'] = $old_category->Title;
            $row['old_category']['id'] = $old_category->ID;
            $row['requester'] = $request->Reqester()->getName();
            $row['has_selections'] = $request->Presentation()->isSelectedByAnyone();
            $row['approver'] = $request->AdminApprover()->getName();
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
    public function handleResolveCategoryChange(SS_HTTPRequest $r)
    {

        if (!is_numeric($r->param('ID'))) {
            return $this->httpError(500, "Invalid category change id");
        }

        $vars = Convert::json2array($r->getBody());        
        if(!isset($vars['approved'])) {
        	return $this->httpError(500, "Request body must contain 'approved' 1 or 0");	
        }
        $approved = (boolean) $vars['approved'];
        $reason = (isset($vars['reason'])) ? $vars['reason'] : 'No reason.';

        $request = SummitCategoryChange::get()->byID($r->param('ID'));
        if(!$request) {
        	return $this->httpError(500, "Request " . $r->param('ID') . " does not exist");
        }

        $new_category = PresentationCategory::get()->byID($request->NewCategoryID);

        if(!$new_category->isTrackChair(Member::currentUserID()) && !Permission::check('ADMIN')) {
            return $this->httpError(403);
        }
        
    	$status = $approved ? SummitCategoryChange::STATUS_APPROVED : SummitCategoryChange::STATUS_REJECTED;

        if ($request->Presentation()->isSelectedByAnyone()) {
            return new SS_HTTPResponse("The presentation has already been selected by chairs.", 500);
        }

        if ($request->Presentation()->isGroupSelected()) {
            return new SS_HTTPResponse("The presentation is on the Team List.", 500);
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

		$oldCat = $request->Presentation()->Category();

        if($approved) {
	        $request->Presentation()->CategoryID = $request->NewCategoryID;
            $request->Presentation()->TrackChairViews()->removeAll(); // empty viewed
	        $request->Presentation()->write();    
	        $request->Presentation()->addNotification(
	        	'{member} approved ' . 
	        	$request->Reqester()->getName() .'\'s request to move this presentation from ' . 
	        	$oldCat->Title . ' to ' .
	        	$category->Title
	        );
    	} else {
    		$request->Presentation()->addNotification(
	        	'{member} rejected ' .
	        	$request->Reqester()->getName() .'\'s request to move this presentation from ' .
	        	$oldCat->Title . ' to ' .
	        	$category->Title.' because : '.$reason
    		);
    	}

        $request->OldCategoryID = $oldCat->ID;
        $request->AdminApproverID = Member::currentUserID();
        $request->Status = $status;
        $request->ApprovalDate = SS_Datetime::now();
        $request->Reason = $reason;
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
            $p->Status = $status;
            $p->ApprovalDate = SS_Datetime::now();
            $p->Reason = $reason;
            $p->write();
        }

        //send email to chairs from both categories
        $new_cat_chairs = $new_category->TrackChairs();
        $old_cat_chairs = $oldCat->TrackChairs();
        $chairs_emails = array();

        foreach($new_cat_chairs as $chair) {
            $chairs_emails[] = $chair->Member()->Email;
        }

        foreach($old_cat_chairs as $chair) {
            $chairs_emails[] = $chair->Member()->Email;
        }

        $subject = 'Track Change '.($approved ? 'Approved' : 'Rejected');
        $body = 'Request submitted by '.$request->Reqester()->getName().' to change presentation "'.$request->Presentation()->Title.'"';
        $body .= ' from track '.$oldCat->Title.' to '.$new_category->Title. ' was '.($approved ? 'approved' : 'rejected');
        $body .= ' by '.Member::currentUser()->getName().'.<br>';

        if (!$approved) {
            $body .= 'The reason for the rejection was the following:<br>';
            $body .= '"'.$reason.'".';
        }

        $email = EmailFactory::getInstance()->buildEmail(
            null,
            implode(',',$chairs_emails),
            $subject,
            $body
        );
        $email->send();

        return $this->ok('change request accepted.');
    }

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

    public function handlePresentationsExport(SS_HTTPRequest $r)
    {
        $activeSummit = Summit::get_active();
        $summitID = $activeSummit->ID;
        $filepath = Controller::join_links(
        	BASE_PATH,
        	ASSETS_DIR,
        	'track-chairs-presentations.csv'
        );

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
        if ($keyword = $r->getVar('search')) {
            if (strpos($keyword, 'Tag:') === 0) {
                $tag = substr($keyword, 4);
                $presentations = $presentations->leftJoin(
                    "SummitEvent_Tags",
                    "Presentation.ID = SummitEvent_Tags.SummitEventID"
                )
                    ->leftJoin(
                        "Tag",
                        "Tag.ID = SummitEvent_Tags.TagID"
                    )->where("Tag.Tag = '{$tag}' ");
            } else {
                $presentations = Presentation::apply_search_query($presentations, $keyword);
            }
        }

        $fp = fopen($filepath, 'w');

        // Setup file to be UTF8
        fprintf($fp, chr(0xEF) . chr(0xBB) . chr(0xBF));

        fputcsv($fp, ['ID', 'Title', 'Speakers', 'Type', 'Track', 'Abstract']);

        foreach($presentations as $pres) {
            $speakers = [];
            foreach($pres->Speakers() as $speaker) {
                $speakers[] = $speaker->getName();
            }

            $fields = [
                $pres->ID,
                $pres->Title,
                implode(', ', $speakers),
                $pres->Type()->Type,
                $pres->Category()->Title,
                strip_tags($pres->Abstract)
            ];

            fputcsv($fp, $fields);
        }

        fclose($fp);

        header("Cache-control: private");
        header("Content-type: application/force-download");
        header("Content-transfer-encoding: binary\n");
        header("Content-disposition: attachment; filename=\"track-chairs-presentations.csv\"");
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

    use RestfulJsonApiResponses;

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
     * @param AbstractRestfulJsonApi $parent
     */
    public function __construct(Presentation $presentation, AbstractRestfulJsonApi $parent)
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
        $data = [];

        if ($current_summit->isSelectionOpen()) {
            foreach ($p->getSpeakersAndModerators() as $s) {
                // if($s->Bio == NULL) $s->Bio = "&nbsp;";
                $s->Bio = str_replace(array("\r", "\n"), "", $s->Bio);
                $speakerData = $s->toJSON();
                $speakerData['photo_url'] = $s->ProfilePhoto();
                $speakerData['available_for_bureau'] = intval($speakerData['available_for_bureau']);
                $speakerData['is_moderator'] = (boolean)$s->ModeratorPresentations()->byID($p->ID);
                $speakerData['profile_link'] = $s->getProfileLink();
                $speakerData['avg_rate_width'] = (float)($s->getAvgFeedback($current_summit) * 20);

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
                $comment['ago'] = $c->obj('Created')->Ago(false);
                $comment['is_activity'] = (boolean)$c->IsActivity;
                $comment['is_public'] = (boolean)$c->IsPublic;
                $comments[] = $comment;
            }

            // remove unsafe character for JSON
            $p->Abstract = ($p->Abstract != null) ? str_replace(array("\r", "\n"), "",
                $p->Abstract) : '(no description provided)';
            $p->ProblemAddressed = ($p->ProblemAddressed != null) ? str_replace(array("\r", "\n"), "",
                $p->ProblemAddressed) : '(no answer provided)';
            $p->AttendeesExpectedLearnt = ($p->AttendeesExpectedLearnt != null) ? str_replace(array("\r", "\n"), "",
                $p->AttendeesExpectedLearnt) : '(no answer provided)';

            $data = $p->toJSON();
            $data['title'] = $p->Title;
            $data['description'] = ($p->Abstract != null) ? $p->Abstract : '(no description provided)';
            $data['social_desc'] = ($p->SocialSummary != null) ? $p->SocialSummary : '(no description provided)';
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
            $data['selectors'] = array_keys($p->getSelectors()->map('Name', 'Name')->toArray());
            $data['likers'] = array_keys($p->getLikers()->map('Name', 'Name')->toArray());
            $data['passers'] = array_keys($p->getPassers()->map('Name', 'Name')->toArray());
            $data['popularity'] = $p->getPopularityScore();
            $data['group_selected'] = $p->isGroupSelected();
            $data['moved_to_category'] = $p->movedToThisCategory();
            $data['change_requests_count'] = SummitCategoryChange::get()->filter([
                'PresentationID' => $p->ID,
                'Status' => SummitCategoryChange::STATUS_PENDING
            ])->count();
            $data['tags'] = $p->getTags()->toNestedArray();
            $data['type'] = $p->Type()->Type;
        }


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
        $isPublic = $r->postVar('is_public') ? true : false;

        if ($comment != null) {
            $commentObj = $this->presentation->addComment($comment, Member::currentUserID(), $isPublic);

            // send push notification
            PublisherSubscriberManager::getInstance()->publish(ISummitEntityEvent::UpdatedEntity, [$this->presentation]);

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

            $toAddress = 'speakersupport@openstack.org';
            $ccAddresses = [];

            foreach($this->presentation->getSpeakersAndModerators() as $s) {
                $ccAddresses[] = $s->getEmail();
            }
            
            $chairs = $this->presentation->Category()->TrackChairs()
            			->exclude('MemberID', $current_user->ID);

            foreach($chairs as $chair) {
            	$ccAddresses[] = $chair->Member()->Email;
            }

            $subject = "Track chair {$current_user->getName()} has a question about your presentation";
            $body = $email;
            $email = EmailFactory::getInstance()->buildEmail(
            	TRACK_CHAIR_TOOL_EMAIL_FROM,
                $toAddress,
            	$subject,
            	$body
            );

            $email->setCC(implode(',', $ccAddresses));
            $email->replyTo($current_user->Email);
            
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
    public function handleSelect(SS_HTTPRequest $r)
    {
        if (!Member::currentUser()) {
            return $this->httpError(403);
        }

        try {
            $maybe    = SummitSelectedPresentation::COLLECTION_MAYBE;
            $pass     = SummitSelectedPresentation::COLLECTION_PASS;
            $selected = SummitSelectedPresentation::COLLECTION_SELECTED;

            switch ($r->getVar('type')) {
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
        catch(EntityValidationException $ex1){
            SS_Log::log($ex1->getMessage(), SS_Log::WARN);
            return $this->validationError($ex1->getMessages());
        }
        catch(Exception $ex){
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
            return $this->serverError();
        }
    }

    /**
     * @param SS_HTTPResponse $r
     * @return SS_HTTPResponse|void
     * @throws SS_HTTPResponse_Exception
     */
    public function handleUnselect(SS_HTTPRequest $r)
    {
        if (!Member::currentUser()) {
            return $this->httpError(403);
        }

        $this->presentation->removeFromIndividualList();

        return new SS_HTTPResponse("Presentation unselected.", 200);

    }

    public function handleMarkAsViewed(SS_HTTPRequest $r)
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
    public function handleGroupSelect(SS_HTTPRequest $r)
    {
        if (!Member::currentUser()) {
            return $this->httpError(403);
        }

        $this->presentation->assignToGroupList();

        // send push notification
        PublisherSubscriberManager::getInstance()->publish(ISummitEntityEvent::UpdatedEntity, [$this->presentation]);

        return new SS_HTTPResponse('OK');

    }

    /**
     * @param SS_HTTPResponse $r
     * @return SS_HTTPResponse|void
     * @throws SS_HTTPResponse_Exception
     */
    public function handleGroupUnselect(SS_HTTPRequest $r)
    {
        if (!Member::currentUser()) {
            return $this->httpError(403);
        }

        $this->presentation->removeFromGroupList();

        // send push notification
        PublisherSubscriberManager::getInstance()->publish(ISummitEntityEvent::UpdatedEntity, [$this->presentation]);

        return new SS_HTTPResponse("Presentation unselected.", 200);

    }

    /**
     * @param SS_HTTPRequest $r
     * @return SS_HTTPResponse|void
     * @throws SS_HTTPResponse_Exception
     * @throws ValidationException
     * @throws null
     */
    public function handleCategoryChangeRequest(SS_HTTPRequest $r)
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

            // send push notification
            PublisherSubscriberManager::getInstance()->publish(ISummitEntityEvent::UpdatedEntity, [$this->presentation]);

            $this->presentation->addNotification('
            	{member} submitted a request to change the category from '. 
            	$request->Presentation()->Category()->Title . ' to ' .
            	$c->Title
            );           

            // send email to chairs
            $chairs = $c->TrackChairs();
            $chairs_emails = array();
            foreach ($chairs as $chair) {
                $chairs_emails[] = $chair->Member()->Email;
            }

            $review_link = Director::baseURL().'track-chairs/change-requests';

            $subject = 'Track Change Requested';
            $body = Member::currentUser()->getName() . ' requested to change the track for presentation ';
            $body .= '"' . $this->presentation->Title . '" from ' . $this->presentation->Category()->Title . ' to ' . $c->Title .'.';
            $body .= '<br>Please review here: <a href="'.$review_link.'">'.$review_link.'</a>.';

            $email = EmailFactory::getInstance()->buildEmail(null, implode(',', $chairs_emails), $subject, $body);
            $email->send();

            return new SS_HTTPResponse("change request made.", 200);

        }
    }
}
