<?php
/**
 * Copyright 2014 Openstack Foundation
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 **/

/**
 * Used to vote on summit presentations
 */
class PresentationVotingPage extends Page
{

    /**
     * @var array
     */
    private static $defaults = array(
        'ShowInMenus' => false
    );

}

/**
 * Class PresentationVotingPage_Controller
 */
class PresentationVotingPage_Controller extends Page_Controller
{

    /**
     * @var array
     */
    private static $url_handlers = array(
        'api' => 'handleAPI',
        '$Action/$ID/$OtherID' => 'handleIndex'
    );


    /**
     * @var array
     */
    private static $allowed_actions = array(
        'handleAPI',
        'handleIndex'
    );


    /**
     *
     */
    public function init()
    {
        parent::init();

        Requirements::clear();
    }


    /**
     * @param SS_HTTPRequest $r
     * @return array|RequestHandler|SS_HTTPResponse|string
     */
    public function handleAPI(SS_HTTPRequest $r)
    {
        $request = new PresentationVotingPage_API(
            Summit::get_active(),
            $this->config()->presentation_limit
        );

        return $request->handleRequest($r, DataModel::inst());
    }


    /**
     * @param SS_HTTPRequest $r
     * @return $this
     */
    public function handleIndex(SS_HTTPRequest $r)
    {
        // No one should be able to deep link to the presentation other than priviledged users
        if ($r->param('Action') === 'presentation' && is_numeric($r->param('ID'))) {
            $presentation = Presentation::get()->byID($r->param('ID'));
            if(!$presentation) {
            	return $this->httpError(404);
            }
            if(!$presentation->canEdit()) {
            	return $this->redirect($this->Link());	
            }
            
        }

        return $this;
    }


    /**
     * @return string
     */
    public function getJSONConfig()
    {
        $s = Summit::get_active();
        return Convert::array2json([
            'baseURL' => $this->Link(),
            'summitTitle' => $s->Title,
            'summitLink' => $this->Parent()->Link(),
            'votingOpen' => $s->isVotingOpen(),
            'loggedIn' => !!Member::currentUser(),
            'presentationLimit' => $this->config()->presentation_limit
        ]);
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

}

/**
 * Class PresentationVotingPage_API
 */
class PresentationVotingPage_API extends RequestHandler
{


    /**
     * @var array
     */
    private static $url_handlers = array(
        'GET presentations.json' => 'handlePresentations',
        'GET summit.json' => 'handleSummit',
        'GET categories.json' => 'handleCategories',
        'GET presentation/$ID' => 'handleReadPresentation',
        'POST presentation/$ID' => 'handleUpdatePresentation',
        'DELETE presentation/$ID' => 'handleDeleteComment'
    );

    /**
     * @var array
     */
    private static $allowed_actions = array(
        'handlePresentations',
        'handleSummit',
        'handleCategories',
        'handleReadPresentation',
        'handleUpdatePresentation',
        'handleDeleteComment'
    );


    /**
     * @var Summit
     */
    protected $summit;


    /**
     * @var int
     */
    protected $limit;


    /**
     * PresentationVotingPage_API constructor.
     * @param Summit $summit
     * @param int $limit
     */
    public function __construct(Summit $summit, $limit = 100)
    {
        $this->summit = $summit;
        $this->limit = $limit;

        parent::__construct();
    }


    /**
     * @param SS_HTTPRquest $r
     * @throws SS_HTTPResponse_Exception
     */
    public function index(SS_HTTPRquest $r)
    {
        return $this->httpError(404);
    }


    /**
     * @param SS_HTTPRequest $r
     * @return SS_HTTPRequest
     */
    public function handlePresentations(SS_HTTPRequest $r)
    {
        $presentations = [];
        $offset = $r->getVar('offset') ?: 0;
        $m = Member::currentUser();
        $list = $m ? $m->getRandomisedPresentations(null, $this->summit) : $this->summit->VoteablePresentations();

        if($list) {
	        if ($r->getVar('category')) {
	            $list = $list->filter(['CategoryID' => $r->getVar('category')]);
	        }

	        if ($r->getVar('search')) {
	            $list = Presentation::apply_search_query($list, $r->getVar('search'));
	        }

	        $total = $list->count();
	        $list = $list->limit($this->limit, $offset);
    	}
    	else {
    		$list = [];
    		$total = 0;
    	}

        foreach ($list as $p) {
            $vote = $p->getUserVote();
            $presentations[] = [
                'id' => $p->ID,
                'title' => $p->Title,
                'user_vote' => $vote ? $vote->Vote : null
            ];
        }

        $result = [
            'presentations' => $presentations,
            'total' => $total
        ];

        return (new SS_HTTPResponse(Convert::array2json($result), 200))
            ->addHeader('Content-Type', 'application/json');
    }


    /**
     * @param SS_HTTPRequest $r
     * @return SS_HTTPRequest|void
     * @throws SS_HTTPResponse_Exception
     */
    public function handleReadPresentation(SS_HTTPRequest $r)
    {
        $presentation = $this->getFromFilename($r->param('ID'), 'Presentation');

        if (!$presentation) {
            return $this->httpError(404);
        }

        $vote = $presentation->getUserVote();
        $json = [
            'id' => $presentation->ID,
            'title' => $presentation->Title,
            'category' => $presentation->Category()->Title,
            'speakers' => [],
            'user_vote' => $vote ? $vote->Vote : null,
            'user_comment' => $vote && $vote->Content ? [
                'id' => $vote->ID,
                'comment' => nl2br($vote->Content),
                'date' => $vote->obj('Created')->Format('F j, Y'),
                'ago' => $vote->obj('Created')->Ago()
            ] : null,
            'abstract' => $presentation->Description,
        ];

        foreach ($presentation->getSpeakersAndModerators() as $s) {
            $json['speakers'][] = [
                'first_name' => $s->FirstName,
                'last_name' => $s->LastName,
                'bio' => $s->Bio,
                'photoUrl' => ($s->Photo()->exists() && Director::fileExists($s->Photo()->URL)) ?
                    $s->Photo()->SetRatioSize(80, 80)->URL :
                    $this->ThemeDir() . '/images/generic-profile-photo.png'
            ];

        }

        $userIsSpeaker = $presentation->getSpeakersAndModerators()
            ->filter('MemberID', Member::currentUserID())
            ->exists();

        $userIsTrackChair = Summit::get_active()
        	->Categories()
        	->relation('TrackChairs')
        	->filter('MemberID', Member::currentUserID())
        	->exists();

        if ($userIsSpeaker || $userIsTrackChair || Permission::check('ADMIN')) {
            $json['all_comments'] = [];
            $votes = $presentation->Votes()
                ->where('Content IS NOT NULL')
                ->sort('Created ASC');

            foreach ($votes as $v) {
                $json['all_comments'][] = [
                    'id' => $v->ID,
                    'comment' => nl2br($v->Content),
                    'author' => $v->Member()->getName(),
                    'date' => $v->obj('Created')->Format('F j, Y'),
                    'ago' => $v->obj('Created')->Ago()
                ];
            }
        }
        return (new SS_HTTPResponse(Convert::array2json($json), 200))
            ->addHeader('Content-Type', 'application/json');
    }


    /**
     * @param SS_HTTPRequest $r
     * @return SS_HTTPResponse|void
     * @throws SS_HTTPResponse_Exception
     */
    public function handleUpdatePresentation(SS_HTTPRequest $r)
    {
        if (!Member::currentUser()) {
            return $this->httpError(403, 'You must be logged in to vote');
        }

        $presentation = $this->getFromFilename($r->param('ID'), 'Presentation');

        if (!$presentation) {
            return $this->httpError(404);
        }

        if(!$presentation->Summit()->isVotingOpen()) {
        	return $this->httpError(403,'Voting is closed');
        }

        $vars = Convert::json2array($r->getBody());

        if (isset($vars['vote'])) {
            $presentation->setUserVote((int)$vars['vote']);

            return new SS_HTTPResponse('OK', 200);
        }

        if (isset($vars['comment'])) {
            if ($userVote = $presentation->getUserVote()) {
                $userVote->Content = $vars['comment'];
                $userVote->write();

                return new SS_HTTPResponse('OK', 200);
            }

            return new SS_HTTPResponse('No vote found', 403);
        }

        return $this->httpError(400);
    }


    /**
     * @param SS_HTTPRequest $r
     * @return SS_HTTPResponse|void
     * @throws SS_HTTPResponse_Exception
     */
    public function handleDeleteComment(SS_HTTPRequest $r)
    {
        if (!Member::currentUser()) {
            return $this->httpError(403, 'You must be logged in to vote');
        }

        $presentation = $this->getFromFilename($r->param('ID'), 'Presentation');

        if (!$presentation) {
            return $this->httpError(404);
        }

        if(!$presentation->Summit()->isVotingOpen()) {
        	return $this->httpError(403,'Voting is closed');
        }


        $userVote = $presentation->getUserVote();
        $userVote->Content = null;
        $userVote->write();

        return new SS_HTTPResponse('OK', 200);
    }


    /**
     * @param SS_HTTPRequest $r
     * @return SS_HTTPRequest
     */
    public function handleSummit(SS_HTTPRequest $r)
    {
        return (new SS_HTTPResponse(Convert::array2json($this->summit->toJSON()), 200))
            ->addHeader('Content-Type', 'application/json');
    }


    /**
     * @param SS_HTTPRequest $r
     * @return SS_HTTPRequest
     */
    public function handleCategories(SS_HTTPRequest $r)
    {
        $result = [];
        foreach ($this->summit->getCategories()->filter('VotingVisible', true) as $c) {
            $result[] = [
                'id' => $c->ID,
                'title' => $c->Title
            ];
        }

        return (new SS_HTTPResponse(Convert::array2json($result), 200))
            ->addHeader('Content-Type', 'application/json');

    }


    /**
     * @param $file
     * @param $class
     * @return mixed
     */
    protected function getFromFilename($file, $class)
    {
        $info = pathinfo($file);
        $id = $info['filename'];
        $list = $class::get();

        if ($class === 'Presentation') {
            $list = $list->filter('Category.VotingVisible', true);
        }

        return $list->byID($id);
    }
}
