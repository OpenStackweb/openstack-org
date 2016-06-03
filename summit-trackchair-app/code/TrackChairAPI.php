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
        'GET chair/add' => 'handleAddChair',
        'GET categorychange/accept/$ID' => 'handleAcceptCategoryChange',
        'GET export/speakerworksheet' => 'handleSpeakerWorksheet',
        'GET restoreorders' => 'handleRestoreOrders',
        'GET presentationcomments' => 'handlePresentationsWithComments'
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
        'handleAcceptCategoryChange' => 'ADMIN',
        'handleRestoreOrders' => 'ADMIN',
        'handleSpeakerWorksheet' => 'ADMIN',
        'handlePresentationsWithComments' => 'ADMIN'
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

        $data['categories'] = array();
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
                $chairdata = array();
                $chairdata['first_name'] = $chair->Member()->FirstName;
                $chairdata['last_name'] = $chair->Member()->Surname;
                $chairdata['email'] = $chair->Member()->Email;
                $chairdata['category'] = $c->Title;
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
                'selected' => $p->isSelected(),
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
            $selections = $list->SummitSelectedPresentations()->sort('Order ASC');
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
                'mine' => $list->mine()
            ];

            foreach ($selections as $s) {
                $data['selections'][] = [
                    'title' => $s->Presentation()->Title,
                    'order' => $s->Order,
                    'id' => $s->PresentationID
                ];
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
        $sortOrder = $vars['sort_order'];
        $listID = $vars['list_id'];        	
        $list = SummitSelectedPresentationList::get()->byId($listID);

        if (is_array($sortOrder)) {
            foreach ($sortOrder as $key => $id) {
                $selection = SummitSelectedPresentation::get()->filter([
                    'PresentationID' => $id,
                    'SummitSelectedPresentationListID' => $listID
                ]);

                // Add the selection if it's new
                if (!$selection->exists()) {
                    $presentation = Presentation::get()->byId($id);
                    if ($presentation->exists() && $presentation->CategoryID == $list->CategoryID) {
                        $s = SummitSelectedPresentation::create();
                        $s->SummitSelectedPresentationListID = $listID;
                        $s->PresentationID = $presentation->ID;
                        $s->MemberID = Member::currentUserID();
                        $s->Order = $key + 1;
                        $s->write();
                    }

                }

                // Adjust the order if not
                if ($selection->exists()) {
                    $s = $selection->first();
                    $s->Order = $key + 1;
                    $s->write();
                }
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
        $summitID = Summit::get_active()->ID;
        $page_size = $r->getVar('page_size') ?: $this->config()->default_page_size;
        $page = $r->getVar('page') ?: 1;

        $changeRequests = SummitCategoryChange::get()
            ->innerJoin('Presentation', 'Presentation.ID = PresentationID')
            ->innerJoin('SummitEvent', 'Presentation.ID = SummitEvent.ID')
            ->filter('SummitEvent.SummitID', $summitID)
            ->sort('Done', 'ASC');

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
            $row['done'] = $request->Done;
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
     * @return SS_HTTPResponse
     */
    public function handlePresentationsWithComments(SS_HTTPRequest $r)
    {
        // Gets a list of presentations that have chair comments
        $page_size = $r->getVar('page_size') ?: $this->config()->default_page_size;
        $page = $r->getVar('page') ?: 1;
        $summitID = Summit::get_active()->ID;

        // Get a collection of chair-visible presentations with comments
        $comments = SummitPresentationComment::get()
            ->leftJoin("Presentation", "SummitPresentationComment.PresentationID = Presentation.ID")
            ->leftJoin("SummitEvent", "SummitEvent.ID = Presentation.ID")
            ->filter([
                'SummitEvent.SummitID' => $summitID,
                'Category.ChairVisible' => true,
                'Presentation.Status' => Presentation::STATUS_RECEIVED
            ])
            ->sort('Created', 'DESC');

        $offset = ($page - 1) * $page_size;
        $count = intval($comments->count());
        $comments = $comments->limit($page_size, $offset);

        $data = [
            'page' => $page,
            'total_pages' => ceil($count / $page_size),
            'results' => array(),
            'has_more' => $count > ($page_size * ($page)),
            'total' => $count,
            'remaining' => $count - ($page_size * ($page))
        ];

        foreach ($comments as $c) {
            $system = strpos(
                $c->Body,
                "suggested that this presentation be moved"
            ) || strpos(
                $c->Body,
                "presentation was moved into the category"
            );

            $data['results'][] = [
                'id' => $c->ID,
                'body' => $c->Body,
                'presentation_title' => $c->Presentation()->Title,
                'presentation_id' => $c->Presentation()->ID,
                'commenter' => $c->Commenter()->FirstName . ' ' . $c->Commenter()->Surname,
                'system_comment' => $system
            ];

        }
        return $this->ok($data);
    }

    /**
     * @param SS_HTTPRequest $r
     * @return string
     */
    public function handleAddChair(SS_HTTPRequest $r)
    {
        $email = $r->getVar('email');
        $catid = $r->getVar('cat_id');
        $category = PresentationCategory::get()->byID($catid);
        if (!$category) {
            return 'category not found';
        }

        $member = Member::get()->filter('Email', $email)->first();
        if (!$member) {
            return 'member not found';
        }

        SummitTrackChair::addChair($member, $catid);
        $category->MemberList($member->ID);
        $category->GroupList();

        return $member->FirstName . ' ' . $member->Surname . ' added as a chair to category ' . $category->Title;

    }

    /**
     * @param SS_HTTPResponse $r
     * @return SS_HTTPResponse|void
     * @throws SS_HTTPResponse_Exception
     * @throws ValidationException
     * @throws null
     */
    public function handleAcceptCategoryChange(SS_HTTPResponse $r)
    {

        if (!Permission::check('ADMIN')) {
            return $this->httpError(403);
        }

        if (!is_numeric($r->param('ID'))) {
            return $this->httpError(500, "Invalid category change id");
        }

        $request = SummitCategoryChange::get()->byID($r->param('ID'));

        if ($request->exists()) {
            if ($request->Presentation()->isSelectedByAnyone()) {
                return new SS_HTTPResponse("The presentation has already been selected by chairs.", 500);
            }

            if ($request->Presentation()->CategoryID == $request->NewCategoryID) {
                $request->Done = true;
                $request->write();

                return new SS_HTTPResponse("The presentation is already in this category.", 200);
            }

            // Make the category change
            $summit = Summit::get_active();
            $category = $summit->Categories()->filter('ID', $request->NewCategoryID)->first();
            if (!$category->exists()) {
                return $this->httpError(500, "Category not found in current summit");
            }

            $request->OldCategoryID = $request->Presentation()->CategoryID;

            $request->Presentation()->CategoryID = $request->NewCategoryID;
            $request->Presentation()->write();

            $comment = SummitPresentationComment::create();
            $comment->Body = 'This presentation was moved into the category '
                . $category->Title . '.'
                . ' The change was approved by '
                . Member::currentUser()->FirstName . ' ' . Member::currentUser()->Surname . '.';
            $comment->PresentationID = $request->Presentation()->ID;
            $comment->write();

            $request->AdminApproverID = Member::currentUserID();
            $request->Approved = true;
            $request->Done = true;
            $request->ApprovalDate = SS_Datetime::now();

            $request->write();

            return $this->ok('change request accepted.');
        }
    }


    /**
     *
     */
    public function handleSpeakerWorksheet()
    {

        $activeSummit = Summit::get_active();
        $filepath = $_SERVER['DOCUMENT_ROOT'] . '/assets/speaker-worksheet.csv';
        $fp = fopen($filepath, 'w');

        // Setup file to be UTF8
        fprintf($fp, chr(0xEF) . chr(0xBB) . chr(0xBF));

        $speakers = PresentationSpeaker::get()
            ->filter('SummitID', $activeSummit->ID);

        foreach ($speakers as $speaker) {
            $fullName = $speaker->FirstName . ' ' . $speaker->LastName;

            foreach ($speaker->Presentations() as $presentation) {
                $fields = [
                    $speaker->ID,
                    $fullName,
                    $speaker->Member()->Email,
                    $presentation->ID,
                    $presentation->Category()->Title,
                    $presentation->Title,
                    $presentation->SelectionStatus()
                ];
                fputcsv($fp, $fields);
            }

        }

        fclose($fp);

        header("Cache-control: private");
        header("Content-type: application/force-download");
        header("Content-transfer-encoding: binary\n");
        header("Content-disposition: attachment; filename=\"speaker-worksheet.csv\"");
        header("Content-Length: " . filesize($filepath));
        readfile($filepath);

    }


    /**
     *
     */
    public function handleRestoreOrders()
    {
        $activeSummit = Summit::get_active();
        $summitCategories = PresentationCategory::get()->filter('SummitID', $activeSummit->ID);

        foreach ($summitCategories as $category) {
            // Grab the track chairs selections for the category

            $selectedPresentationList = SummitSelectedPresentationList::get()
                ->filter('CategoryID', $category->ID);

            foreach ($selectedPresentationList as $list) {
                $selections = $list->SummitSelectedPresentations()->sort('Order', 'ASC');
                $i = 1;
                foreach ($selections as $selection) {
                    $selection->Order = $i;
                    $selection->write();
                    $i++;
                }
            }
        }

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
    private static $url_handlers = array(
        'GET ' => 'index',
        'POST vote' => 'handleVote',
        'POST comment' => 'handleAddComment',
        'PUT select' => 'handleSelect',
        'PUT unselect' => 'handleUnselect',
        'PUT group/select' => 'handleGroupSelect',
        'PUT group/unselect' => 'handleGroupUnselect',
        'GET categorychange/new' => 'handleCategoryChangeRequest',
    );


    /**
     * @var array
     */
    private static $allowed_actions = array(
        'handleVote',
        'handleAddComment',
        'handleSelect',
        'handleUnselect',
        'handleCategoryChangeRequest',
        'handleGroupSelect',
        'handleGroupUnselect'
    );


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
        $speakers = array();
        $current_summit = $p->Summit();;

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

            $former_presentations = array();
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

        foreach ($p->Comments()->filter('IsCategoryChangeSuggestion', false) as $c) {
            $comment = $c->toJSON();
            $comment['name'] = $c->Commenter()->FirstName . ' ' . $c->Commenter()->Surname;
            $comment['ago'] =  $c->obj('Created')->Ago(false);
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
        $data['selected'] = $p->isSelected();
        $data['group_selected'] = $p->isGroupSelected();
        $data['moved_to_category'] = $p->movedToThisCategory();

        return (new SS_HTTPResponse(
            Convert::array2json($data), 200
        ))->addHeader('Content-Type', 'application/json');

    }

    /**
     * @param SS_HTTPRequest $r
     * @return SS_HTTPResponse|void
     * @throws SS_HTTPResponse_Exception
     */
    public function handleVote(SS_HTTPRequest $r)
    {
        if (!Member::currentUser()) {
            return $this->httpError(403);
        }

        $vote = $r->postVar('vote');
        if ($vote >= -1 && $vote <= 3) {
            $this->presentation->setUserVote($vote);

            return new SS_HTTPResponse(null, 200);
        }

        return $this->httpError(400, "Invalid vote");
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
     * @param SS_HTTPResponse $r
     * @throws SS_HTTPResponse_Exception
     */
    public function handleSelect(SS_HTTPResponse $r)
    {
        if (!Member::currentUser()) {
            return $this->httpError(403);
        }

        $this->presentation->assignToIndividualList();

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

        if (!is_numeric($r->getVar('new_cat'))) {
            return $this->httpError(500, "Invalid category id");
        }

        $c = PresentationCategory::get()->byID((int)$r->getVar('new_cat'));

        if ($c) {
            $request = SummitCategoryChange::create();
            $request->PresentationID = $this->presentation->ID;
            $request->NewCategoryID = $r->getVar('new_cat');
            $request->ReqesterID = Member::currentUserID();
            $request->write();

            $m = Member::currentUser();
            $comment = $m->FirstName . ' ' . $m->Surname . ' suggested that this presentation be moved to the category ' . $c->Title . '.';

            $this->presentation->addComment($comment, Member::currentUserID(), true);

            return new SS_HTTPResponse("change request made.", 200);

        }
    }
}
