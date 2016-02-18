<?php

/**
 * Class TrackChairAPI
 */
class TrackChairAPI extends AbstractRestfulJsonApi
{

    protected function isApiCall()
    {
       return true;
    }

    /**
     * @return bool
     */
    protected function authorize()
    {
        return !is_null(Member::currentUser());
    }

    private static $url_handlers = array(
        'summit/$ID' => 'handleSummit',
        'POST presentation/sched/$SchedID!' => 'handleSchedUpdate',
        'presentation/$ID' => 'handleManagePresentation',
        'GET ' => 'handleGetAllPresentations',
        'GET selections/$categoryID' => 'handleGetMemberSelections',
        'POST reorder' => 'handleReorderList',
        'GET change_requests' => 'handleChangeRequests',
        'GET chair/add' => 'handleAddChair',
        'GET category_change/accept/$ID' => 'handleAcceptCategoryChange',
        'GET export/schedule' => 'handleScheduleForSched',
        'GET export/speakers' => 'handleSpeakersForSched',
        'GET export/speaker-worksheet' => 'handleSpeakerWorksheet',
        'GET restore-orders' => 'handleRestoreOrders',
        'GET presentation-comments' => 'handlePresentationsWithComments'
    );

    private static $allowed_actions = array(
        'handleSummit',
        'handleManagePresentation',
        'handleGetAllPresentations',
        'handleSchedUpdate',
        'handleGetMemberSelections',
        'handleReorderList',
        'handleChangeRequests',
        'handleAddChair' => 'ADMIN',
        'handleAcceptCategoryChange' => 'ADMIN',
        'handleScheduleForSched' => 'ADMIN',
        'handleSpeakersForSched' => 'ADMIN',
        'handleRestoreOrders' => 'ADMIN',
        'handleSpeakerWorksheet' => 'ADMIN',
        'handlePresentationsWithComments' => 'ADMIN'
    );

    private static $extensions = array(
        'MemberTokenAuthenticator'
    );

    public function init()
    {
        parent::init();
        $this->checkAuthenticationToken(false);
    }

    private function trackChairDetails()
    {

        if (!Member::currentUser()) {
            return $this->httpError(403);
        }

        $data = array(
            'categories' => null,
        );

        $summit = Summit::get_active();

        $TrackChair = SummitTrackChair::get()->filter('MemberID', Member::currentUserID());

        if ($TrackChair->count()) {

            $chair = $TrackChair->first();

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
        $data['status']                   = $summit->getStatus();
        $data['on_voting_period']         = $summit->isVotingOpen();
        $data['on_selection_period']      = $summit->isSelectionOpen();
        $data['is_selection_period_over'] = $summit->isSelectionOver();

        $data['categories']          = array();
        $data['track_chair']         = $this->trackChairDetails();

        $chairlist = array();
        $categoriesIsChair = array();
        $categoriesNotChair = array();


        foreach ($summit->Categories()->filter('ChairVisible', true) as $c) {

            $isChair = ($c->isTrackChair(Member::currentUserID()) == 1);

            $categoryDetials = array(
                'id' => $c->ID,
                'title' => $c->Title,
                'description' => $c->Description,
                'session_count' => $c->SessionCount,
                'alternate_count' => $c->AlternateCount,
                'summit_id' => $c->SummitID,
                'user_is_chair' => $isChair
            );

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

    public function handleGetAllPresentations(SS_HTTPRequest $r)
    {
        // Gets a list of presentations that have chair comments

        $page_size = $r->getVar('page_size') ?: 20;

        $page = $r->getVar('page') ?: 1;

        $summitID = Summit::get_active()->ID;

        // Get a collection of chair-visible presentation categories
        $presentations = Presentation::get()
            ->leftJoin("PresentationCategory", "PresentationCategory.ID = Presentation.CategoryID")
            ->where("
				SummitEvent.SummitID = {$summitID}
				AND PresentationCategory.ChairVisible = 1
				AND Presentation.Status = 'Received'
				");

        if ($r->getVar('category')) {
            $presentations = $presentations->filter('CategoryID', (int)$r->getVar('category'));
        }
        if ($r->getVar('keyword')) {
            $k = Convert::raw2sql($r->getVar('keyword'));
            $presentations = $presentations
                ->leftJoin("Presentation_Speakers", "Presentation_Speakers.PresentationID = Presentation.ID")
                ->leftJoin("PresentationSpeaker",
                    "PresentationSpeaker.ID = Presentation_Speakers.PresentationSpeakerID")
                ->where("
									SummitEvent.Title LIKE '%{$k}%'
									OR SummitEvent.Description LIKE '%{$k}%'
									OR SummitEvent.ShortDescription LIKE '%{$k}%'
									OR (concat_ws(' ', PresentationSpeaker.FirstName, PresentationSpeaker.LastName)) LIKE '%{$k}%'
								");
        }

        $offset     = ($page - 1 ) * $page_size;
        $count        = $presentations->count();
        $presentations = $presentations->limit($page_size, $offset);

        $data = array(
            'results'     => array(),
            'page'        => $page,
            'total_pages' =>  ceil($count/$page_size),
            'results'     => array(),
            'has_more'    => $count > ($page_size * ($page)),
            'total'       => $count,
            'remaining'   => $count - ($page_size * ($page))
        );

        foreach ($presentations as $p) {
            $data['results'][] = array(
                'id' => $p->ID,
                'title' => $p->Title,
                'selected' => $p->isSelected(),
                'vote_count' => $p->CalcVoteCount(),
                'vote_average' => $p->CalcVoteAverage(),
                'total_points' => $p->CalcTotalPoints(),
                'moved_to_category' => $p->movedToThisCategory(),
            );
        }

        return $this->ok($data);
    }

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

    public function handleGetMemberSelections(SS_HTTPRequest $r)
    {
        $results = [];
        $categoryID = (int)$r->param('categoryID');
        $results['category_id'] = $categoryID;

        $category = PresentationCategory::get()->byID($categoryID);
        if(is_null($category)) return $this->notFound(sprintf('Category id %s not found!',$categoryID));
        $summitID = intval(Summit::get_active()->ID);
        if(intval($category->SummitID) !== $summitID ) return $this->validationError(sprintf('Category id %s does not belong to current summit!',$categoryID));

        $lists = SummitSelectedPresentationList::getAllListsByCategory($categoryID);

        foreach ($lists as $key => $list) {

            $selections = $list->SummitSelectedPresentations()->sort('Order ASC');
            $count = $selections->count();
            $listID = $list->ID;

            $data = array(
                'list_name' => $list->name,
                'list_type' => $list->ListType,
                'list_id' => $listID,
                'total' => $count,
                'can_edit' => $list->memberCanEdit(),
                'slots' => $list->maxPresentations(),
                'mine' => $list->mine()
            );

            foreach ($selections as $s) {
                $data['selections'][] = array(
                    'title' => $s->Presentation()->Title,
                    'order' => $s->Order,
                    'id' => $s->PresentationID
                );
            }

            $results['lists'][] = $data;

        }

        return $this->ok($results);

    }

    public function handleReorderList(SS_HTTPRequest $r)
    {

        $sortOrder = $r->postVar('sort_order');
        $listID    = $r->postVar('list_id');
        $list      = SummitSelectedPresentationList::get()->byId($listID);

        if (is_array($sortOrder)) {
            foreach ($sortOrder as $key => $id) {
                $selection = SummitSelectedPresentation::get()->filter(array(
                    'PresentationID' => $id,
                    'SummitSelectedPresentationListID' => $listID
                ));

                // Add the selection if it's new
                if (!$selection->exists()) {
                    $presentation = Presentation::get()->byId($id);
                    if ($presentation->exists() && $presentation->CategoryID == $list->CategoryID) {
                        $s = new SummitSelectedPresentation();
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

    public function handleChangeRequests(SS_HTTPRequest $r)
    {

        $summitID = Summit::get_active()->ID;

        $page_size = $r->getVar('page_size') ?: 20;

        $page = $r->getVar('page') ?: 1;

        $changeRequests = SummitCategoryChange::get()
            ->innerJoin('Presentation','Presentation.ID = PresentationID')
            ->innerJoin('SummitEvent','Presentation.ID = SummitEvent.ID')
            ->filter(array( 'SummitEvent.SummitID' => $summitID))
            ->sort('Done', 'ASC');

        $results = [];
        $isAdmin = Permission::check('ADMIN');
        $memID = Member::currentUserID();

        $offset         = ($page - 1 ) * $page_size;
        $count          = intval($changeRequests->count());
        $changeRequests = $changeRequests->limit($page_size, $offset);

        $data = array(
            'results'     => array(),
            'page'        => $page,
            'total_pages' =>  ceil($count/$page_size),
            'results'     => array(),
            'has_more'    => $count > ($page_size * ($page)),
            'total'       => $count,
            'remaining'   => $count - ($page_size * ($page))
        );

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
            $data['results'][]  = $row;
        }

        return $this->ok($data);

    }

    public function handlePresentationsWithComments(SS_HTTPRequest $r)
    {

        // Gets a list of presentations that have chair comments

        $page_size = $r->getVar('page_size') ?: 20;


        $page = $r->getVar('page') ?: 1;

        $summitID = Summit::get_active()->ID;

        // Get a collection of chair-visible presentations with comments
        $comments = SummitPresentationComment::get()
            ->leftJoin("Presentation", "SummitPresentationComment.PresentationID = Presentation.ID")
            ->leftJoin("PresentationCategory", "PresentationCategory.ID = Presentation.CategoryID")
            ->leftJoin("SummitEvent", "SummitEvent.ID = Presentation.ID")
            ->where("
				SummitEvent.SummitID = {$summitID}
				AND PresentationCategory.ChairVisible = 1
				AND Presentation.Status = 'Received'
				")
            ->sort('Created', 'DESC');

        $offset     = ($page - 1 ) * $page_size;
        $count       = intval($comments->count());
        $comments    = $comments->limit($page_size, $offset);

        $data = array(
            'page'        => $page,
            'total_pages' =>  ceil($count/$page_size),
            'results'     => array(),
            'has_more'    => $count > ($page_size * ($page)),
            'total'       => $count,
            'remaining'   => $count - ($page_size * ($page))
        );

        foreach ($comments as $c) {

            $system = strpos($c->Body, "suggested that this presentation be moved") || strpos($c->Body,
                    "presentation was moved into the category");

            $data['results'][] = array(
                'id' => $c->ID,
                'body' => $c->Body,
                'presentation_title' => $c->Presentation()->Title,
                'presentation_id' => $c->Presentation()->ID,
                'commenter' => $c->Commenter()->FirstName . ' ' . $c->Commenter()->Surname,
                'system_comment' => $system
            );

        }
       return $this->ok($data);
    }

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

            $comment = new SummitPresentationComment();
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

    public function handleScheduleForSched()
    {

        $this->handleRestoreOrders();

        $filepath = $_SERVER['DOCUMENT_ROOT'] . '/assets/schedule-import.csv';
        $fp = fopen($filepath, 'w');

        // Setup file to be UTF8
        fprintf($fp, chr(0xEF) . chr(0xBB) . chr(0xBF));

        $activeSummit = Summit::get_active();

        $SummitCategories = DataObject::get('PresentationCategory', $activeSummit->ID);

        foreach ($SummitCategories as $Category) {
            // Grab the track chairs selections for the category

            $SelectedPresentationList = SummitSelectedPresentationList::get()
                ->filter(array('CategoryID' => $Category->ID, 'ListType' => 'Group'))
                ->first();


            if ($SelectedPresentationList) {

                // Loop through each selected talk to output the details
                // Note that a SummitSelectedTalk is really just a cross-link table that also contains the priority the talk was given
                foreach ($SelectedPresentationList->SortedPresentations() as $Selection) {

                    $p = $Selection->Presentation();

                    if ($p->SelectionStatus() == 'accepted') {

                        // Build speaker column
                        $Speakers = '';

                        foreach ($p->Speakers() as $Speaker) {
                            $Speakers = $Speakers . $Speaker->FirstName . " ";
                            $Speakers = $Speakers . $Speaker->LastName . ",";
                        }

                        // Output presentation row
                        $fields = array(
                            $Selection->Order,
                            $p->ID,
                            $p->Title,
                            $p->Category()->Title,
                            $p->Description,
                            $Speakers
                        );

                        fputcsv($fp, $fields);
                    }
                }
            }
        }

        fclose($fp);

        header("Cache-control: private");
        header("Content-type: application/force-download");
        header("Content-transfer-encoding: binary\n");
        header("Content-disposition: attachment; filename=\"schedule-import.csv\"");
        header("Content-Length: " . filesize($filepath));
        readfile($filepath);


    }

    public function handleSpeakerWorksheet()
    {

        $activeSummit = Summit::get_active();

        $filepath = $_SERVER['DOCUMENT_ROOT'] . '/assets/speaker-worksheet.csv';

        $fp = fopen($filepath, 'w');

        // Setup file to be UTF8
        fprintf($fp, chr(0xEF) . chr(0xBB) . chr(0xBF));

        $Speakers = PresentationSpeaker::get()
            ->filter('SummitID', $activeSummit->ID);

        foreach ($Speakers as $Speaker) {

            $PhotoURL = "";
            if ($Speaker->PhotoID != 0) {
                $PhotoURL = 'http://www.openstack.org' . $Speaker->Photo()->getURL();
            }
            $fullName = $Speaker->FirstName . ' ' . $Speaker->LastName;

            foreach ($Speaker->Presentations() as $Presentation) {
                $fields = array(
                    $Speaker->ID,
                    $fullName,
                    $Speaker->Member()->Email,
                    $Presentation->ID,
                    $Presentation->Category()->Title,
                    $Presentation->Title,
                    $Presentation->SelectionStatus()
                );
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

    public function handleSpeakersForSched()
    {

        $activeSummit = Summit::get_active();

        $filepath = $_SERVER['DOCUMENT_ROOT'] . '/assets/speaker-import.csv';

        $fp = fopen($filepath, 'w');

        // Setup file to be UTF8
        fprintf($fp, chr(0xEF) . chr(0xBB) . chr(0xBF));

        $Speakers = PresentationSpeaker::get()
            ->filter('SummitID', $activeSummit->ID);

        foreach ($Speakers as $Speaker) {

            $AcceptedPresentations = $Speaker->AcceptedPresentations();

            $PhotoURL = "";

            if ($Speaker->PhotoID != 0) {
                $PhotoURL = 'http://www.openstack.org' . $Speaker->Photo()->getURL();
            }

            if ($Speaker->Bio == null) {
                $SpeakerWithBio = PresentationSpeaker::get()->filter(array(
                    'MemberID' => $Speaker->MemberID,
                    'Bio:not' => null
                ))->first();
                if ($SpeakerWithBio && $SpeakerWithBio->Bio) {
                    $Speaker->Bio = $SpeakerWithBio->Bio;
                }
            }

            if ($AcceptedPresentations->count()) {
                $fullName = $Speaker->FirstName . ' ' . $Speaker->LastName;
                $fields = array(
                    $fullName,
                    $Speaker->Member()->Email,
                    ' ',
                    ' ',
                    $Speaker->Title,
                    ' ',
                    $Speaker->Bio,
                    ' ',
                    $PhotoURL
                );
                fputcsv($fp, $fields);
            }

        }

        fclose($fp);

        header("Cache-control: private");
        header("Content-type: application/force-download");
        header("Content-transfer-encoding: binary\n");
        header("Content-disposition: attachment; filename=\"speaker-import.csv\"");
        header("Content-Length: " . filesize($filepath));
        readfile($filepath);

    }

    public function handleRestoreOrders()
    {

        $activeSummit = Summit::get_active();

        $SummitCategories = DataObject::get('PresentationCategory', $activeSummit->ID);

        foreach ($SummitCategories as $Category) {
            // Grab the track chairs selections for the category

            $SelectedPresentationList = SummitSelectedPresentationList::get()
                ->filter(array('CategoryID' => $Category->ID));

            foreach ($SelectedPresentationList as $list) {
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


class TrackChairAPI_PresentationRequest extends RequestHandler
{


    private static $url_handlers = array(
        'GET ' => 'index',
        'POST vote' => 'handleVote',
        'POST comment' => 'handleAddComment',
        'GET select' => 'handleSelect',
        'GET unselect' => 'handleUnselect',
        'GET group/select' => 'handleGroupSelect',
        'GET group/unselect' => 'handleGroupUnselect',
        'GET category_change/new' => 'handleCategoryChangeRequest',
    );


    private static $allowed_actions = array(
        'handleVote',
        'handleAddComment',
        'handleSelect',
        'handleUnselect',
        'handleCategoryChangeRequest',
        'handleGroupSelect',
        'handleGroupUnselect'
    );


    protected $presentation;


    protected $parent;


    public function __construct(Presentation $presentation, PresentationAPI $parent)
    {
        parent::__construct();
        $this->presentation = $presentation;
        $this->parent = $parent;
    }


    public function index(SS_HTTPRequest $r)
    {

        /* if(!Member::currentUser()) {
            return $this->httpError(403);
        } */

        $p = $this->presentation;
        $speakers = array();

        foreach ($p->Speakers() as $s) {
            // if($s->Bio == NULL) $s->Bio = "&nbsp;";
            $s->Bio = str_replace(array("\r", "\n"), "", $s->Bio);
            $photo_url = null;
            if ($s->Photo()->exists() && $s->Photo()->croppedImage(100, 100)) {
                $photo_url = $s->Photo()->croppedImage(100, 100)->URL;
            }

            $speakerData = $s->toJSON();
            $speakerData['photo_url'] = $photo_url;
            $speakers[] = $speakerData;

        }

        $comments = array();

        foreach ($p->Comments()->filter('IsCategoryChangeSuggestion', 0) as $c) {
            $comment = $c->toJSON();
            $comment['name'] = $c->Commenter()->FirstName . ' ' . $c->Commenter()->Surname;
            $comments[] = $comment;
        }

        // remove unsafe character for JSON
        $p->ShortDescription = ($p->ShortDescription != null) ? str_replace(array("\r", "\n"), "",
            $p->ShortDescription) : '(no description provided)';
        $p->ProblemAddressed = ($p->ProblemAddressed != null) ? str_replace(array("\r", "\n"), "",
            $p->ProblemAddressed) : '(no answer provided)';
        $p->AttendeesExpectedLearnt = ($p->AttendeesExpectedLearnt != null) ? str_replace(array("\r", "\n"), "",
            $p->AttendeesExpectedLearnt) : '(no answer provided)';
        $p->SelectionMotive = ($p->SelectionMotive != null) ? str_replace(array("\r", "\n"), "",
            $p->SelectionMotive) : '(no answer provided)';

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
        $data['comments'] = $comments ? $comments : null;
        $data['can_assign'] = $p->canAssign(1) ? $p->canAssign(1) : null;
        $data['selected'] = $p->isSelected();
        $data['group_selected'] = $p->isGroupSelected();
        $data['moved_to_category'] = $p->movedToThisCategory();

        return (new SS_HTTPResponse(
            Convert::array2json($data), 200
        ))->addHeader('Content-Type', 'application/json');

    }


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

    public function handleAddComment(SS_HTTPRequest $r)
    {
        if (!Member::currentUser()) {
            return $this->httpError(403);
        }

        $comment = $r->postVar('comment');

        if ($comment != null) {
            $this->presentation->addComment($comment, Member::currentUserID());

            return new SS_HTTPResponse(null, 200);
        }

        return $this->httpError(400, "Invalid comment");
    }

    public function handleSelect(SS_HTTPResponse $r)
    {
        if (!Member::currentUser()) {
            return $this->httpError(403);
        }

        $this->presentation->assignToIndividualList();

    }

    public function handleUnselect(SS_HTTPResponse $r)
    {
        if (!Member::currentUser()) {
            return $this->httpError(403);
        }

        $this->presentation->removeFromIndividualList();

        return new SS_HTTPResponse("Presentation unseleted.", 200);

    }

    public function handleGroupSelect(SS_HTTPResponse $r)
    {
        if (!Member::currentUser()) {
            return $this->httpError(403);
        }

        $this->presentation->assignToGroupList();

    }

    public function handleGroupUnselect(SS_HTTPResponse $r)
    {
        if (!Member::currentUser()) {
            return $this->httpError(403);
        }

        $this->presentation->removeFromGroupList();

        return new SS_HTTPResponse("Presentation unseleted.", 200);

    }

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

            $request = new SummitCategoryChange();
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
