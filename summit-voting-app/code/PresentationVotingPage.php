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

class PresentationVotingPage extends Page {

	private static $defaults = array(
		'ShowInMenus' => false
	);

}
 
class PresentationVotingPage_Controller extends Page_Controller {

	private static $url_handlers = array (
		'api' => 'handleAPI',
		'$Action/$ID/$OtherID' => 'handleIndex'
	);


	private static $allowed_actions = array (
		'handleAPI',
		'handleIndex'
	);


	public function init() {
		parent::init();

		Requirements::clear();
	}


	public function handleAPI (SS_HTTPRequest $r) {
		$request = new PresentationVotingPage_API(
			Summit::get_active(),
			$this->config()->presentation_limit
		);

		return $request->handleRequest($r, DataModel::inst());
	}


	public function getJSONConfig() {
		$s = Summit::get_active();
		return Convert::array2json([
			'baseURL' => $this->Link(),
			'summitTitle' => $s->Title,
			'summitLink' => $this->Parent()->Link(),
			'loggedIn' => !!Member::currentUser(),
			'presentationLimit' => $this->config()->presentation_limit
		]);
	}


	public function getAppJSFile() {
		return $this->config()->app_js_file;
	}
}

class PresentationVotingPage_API extends RequestHandler {

	
	private static $url_handlers = array (
		'GET presentations.json' => 'handlePresentations',
		'GET summit.json' => 'handleSummit',
		'GET categories.json' => 'handleCategories',
		'GET presentation/$ID' => 'handleReadPresentation',
		'POST presentation/$ID' => 'handleUpdatePresentation'
	);

	private static $allowed_actions = array (
		'handlePresentations',
		'handleSummit',
		'handleCategories',
		'handleReadPresentation',
		'handleUpdatePresentation'
	);

	
	protected $summit;


	protected $limit;


	public function __construct (Summit $summit, $limit = 100) {
		$this->summit = $summit;
		$this->limit = $limit;

		parent::__construct();
	}


	public function index (SS_HTTPRquest $r) {
		return $this->httpError(404);
	}


	public function handlePresentations (SS_HTTPRequest $r) {
		$presentations = [];
		$offset = $r->getVar('offset') ?: 0;

		$list = SummitEvent::get()
					->innerJoin("Presentation","Presentation.ID = SummitEvent.ID")
					->filter(['SummitID' => $this->summit->ID]);
		
		if($r->getVar('category')) {
			$list = $list->filter(['CategoryID' => $r->getVar('category')]);
		}

		if($r->getVar('search')) {
			$k = Convert::raw2sql($r->getVar('search'));
            $list = $list
             	->leftJoin(
 					"Presentation_Speakers", 
 					"Presentation_Speakers.PresentationID = Presentation.ID"
 				)
  			  	->leftJoin(
  			  		"PresentationSpeaker", 
  			  		"PresentationSpeaker.ID = Presentation_Speakers.PresentationSpeakerID"
  			  	)
  			  	->where("
                  	SummitEvent.Title LIKE '%{$k}%'
                  	OR SummitEvent.Description LIKE '%{$k}%'
                  	OR PresentationSpeaker.FirstName LIKE '%{$k}%'
                  	OR PresentationSpeaker.LastName LIKE '%{$k}%'
                ");
		}

		$total = $list->count();
		$list = $list->limit($this->limit, $offset);		
		
		foreach($list as $p) {
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


	public function handleReadPresentation (SS_HTTPRequest $r) {		
		$presentation = $this->getFromFilename($r->param('ID'), 'Presentation');

		if(!$presentation) return $this->httpError(404);

		$vote = $presentation->getUserVote();
		$json = [
			'id' => $presentation->ID,
			'title' => $presentation->Title,
			'category' => $presentation->Category()->Title,
			'speakers' => [],
			'user_vote' => $vote ? $vote->Vote : null,
			'abstract' => $presentation->Description,
		];

		foreach($presentation->Speakers() as $s) {
			$json['speakers'][] = [
				'first_name' => $s->FirstName,
				'last_name' => $s->LastName,
				'bio' => $s->Bio,
				'photoUrl' => ($s->Photo()->exists() && Director::fileExists($s->Photo()->URL)) ? 
								$s->Photo()->SetRatioSize(80,80)->URL :
								$this->ThemeDir().'/images/generic-profile-photo.png'
			];

		}

		return (new SS_HTTPResponse(Convert::array2json($json), 200))
					->addHeader('Content-Type', 'application/json');
	}


	public function handleUpdatePresentation (SS_HTTPRequest $r) {
		if(!Member::currentUser()) {
			return $this->httpError(403, 'You must be logged in to vote');
		}

		$presentation = $this->getFromFilename($r->param('ID'), 'Presentation');
		
		if(!$presentation) return $this->httpError(404);

		$vars = Convert::json2array($r->getBody());

		if(isset($vars['vote'])) {
			$presentation->setUserVote((int) $vars['vote']);
			
			return new SS_HTTPResponse('OK', 200);
		}

		return $this->httpError(400);
	}


	public function handleSummit (SS_HTTPRequest $r) {
		return (new SS_HTTPResponse(Convert::array2json($this->summit->toJSON()), 200))
					->addHeader('Content-Type', 'application/json');
	}


	public function handleCategories (SS_HTTPRequest $r) {
		$result = [];
		foreach($this->summit->Categories() as $c) {
			$result[] = [
				'id' => $c->ID,
				'title' => $c->Title
			];
		}

		return (new SS_HTTPResponse(Convert::array2json($result), 200))
					->addHeader('Content-Type', 'application/json');

	}


	protected function getFromFilename ($file, $class) {
		$info = pathinfo($file);
		$id = $info['filename'];

		return $class::get()->byID($id);
	}
}
