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
class PresentationSlideSubmissionController extends Page_Controller
{
	/**
	 * @var array
     */
	private static $allowed_actions = [
		'presentations',
		'handlePresentation',
		'emailspeakers' => 'ADMIN'
	];

	/**
	 * @var array
     */
	private static $url_handlers = [
		'presentation/$ID' => 'handlePresentation'
	];

	/**
	 *
     */
	public function init()
	{
		parent::init();

		// sanity check
		if(!Summit::get_most_recent()) {
			throw new Exception('There is no recent summit');
		}
	}

	/**
	 * @param null $action
	 * @return String
     */
	public function Link($action = null)
	{
		return Controller::join_links(
			BASE_URL,
			'submit-slides'
		);
	}

	/**
	 * @param SS_HTTPRequest $r
     */
	public function presentations(SS_HTTPRequest $r)
	{
		$data = [];
		$speaker = null;
		$key = $r->getVar('key');
		if ($key) {
			$username = PresentationSpeaker::hash_to_username($key);
			$speaker = PresentationSpeaker::get()->filter('Member.Email', $username)->first();

		} elseif ($speakerID = Session::get('UploadMedia.SpeakerID')) {
			$speaker = PresentationSpeaker::get()->byID($speakerID);
		}

		// Speaker not found
		if (!$speaker) {
			return $this->httpError(404, 'Sorry, that does not appear to be a valid token.');
		}

		Session::set('UploadMedia.SpeakerID', $speaker->ID);
		
		$mostRecentSummit = Summit::get_most_recent();
		$presentations = $speaker->PublishedPresentations($mostRecentSummit->ID);

		// No presentations
		if (!$presentations->exists()) {
			return $this->httpError(404, 'Sorry, it does not appear that you have any presentations.');
		}

		// IF there's only one presentation with no media, go ahead and forward to it's page
		if ($presentations->count() == 1) {
			$slide = $presentations->first()->MaterialType('PresentationSlide');
			if(!$slide) {
				$presentationID = $presentations->first()->ID;
				return $this->redirect(Controller::join_links(
					$this->Link(),
					'/presentation/',
					$presentationID,
					'upload'
				));
			}
		}

		$data['Speaker'] = $speaker;
		$data['Presentations'] = $presentations;

		return $this->customise($data);
	}

	/**
	 * @param SS_HTTPRequest $r
	 * @return mixed
     */
	public function handlePresentation(SS_HTTPRequest $r)
	{
		$presentationID = $this->request->param("ID");
		// make sure there's a presentation by that id
		$presentation = Presentation::get()->byID($presentationID);
		// pull the speaker from the session and make sure they are a speaker for this presentation
		$speakerID = Session::get('UploadMedia.SpeakerID');

		if ($presentation && $speakerID && $presentation->Speakers()->find('ID', $speakerID)) {
			$request = PresentationSlideSubmissionController_PresentationRequest::create($this, $presentation);

			return $request->handleRequest($r, DataModel::inst());
		}

		return $this->customise([
			'HasError' => true
		]);
	}

	/**
	 * @param SS_HTTPRequest $r
     */
	public function emailspeakers(SS_HTTPRequest $r)
	{
		$summit = Summit::get_most_recent();
		$confirm = $r->getVar('confirm');
		$speakers = PresentationSpeaker::get()
			->innerJoin('Presentation_Speakers','Presentation_Speakers.PresentationSpeakerID = PresentationSpeaker.ID')
			->innerJoin('SummitEvent', 'SummitEvent.ID = Presentation_Speakers.PresentationID')
			->innerJoin('Presentation', 'Presentation.ID = SummitEvent.ID')
			->exclude([
				// Keynotes, Sponsored Sessions, BoF, and Working Groups, vBrownBag
				'Presentation.CategoryID' => [40, 41, 46, 45, 48]
			])
			->filter('SummitID', $summit->ID)
			->limit($confirm ? null : 50);

		foreach ($speakers as $speaker) {
			/* @var DataList */
			$presentations = $speaker->PublishedPresentations($summit->ID);
				// Todo -- how to deal with this?
				// !$speaker->GeneralOrKeynote() &&
				// !SchedSpeakerEmailLog::BeenEmailed($Speaker->email) &&
			if ($presentations->exists() && EmailValidator::validEmail($speaker->Member()->Email)) {
				$to = $speaker->Member()->Email;				
				$subject = "Important Speaker Information for OpenStack Summit in {$summit->Title}";

				$email = EmailFactory::getInstance()->buildEmail('do-not-reply@openstack.org', $to, $subject);
				$email->setTemplate("UploadPresentationSlidesEmail");
				$email->populateTemplate([
					'Speaker' => $speaker,
					'Presentations' => $presentations,
					'Summit' => $summit
				]);

				if ($confirm) {
					//SchedSpeakerEmailLog::addSpeaker($to);
					$email->send();
				} else {
					echo $email->debug();
				}

				echo 'Email sent to ' . $to . ' ('.$speaker->getName().')<br/>';
			}
		}
	}



}

/**
 * Class PresentationSlideSubmissionController_PresentationRequest
 */
class PresentationSlideSubmissionController_PresentationRequest extends Controller
{
	/**
	 * @var array
     */
	private static $allowed_actions = [
		'upload',
		'Form',
		'LinkToForm',
		'linkto',
		'success'
	];

	/**
	 * @var PresentationSlideSubmissionController
     */
	protected $parent;

	/**
	 * @var Presentation
     */
	protected $presentation;

	/**
	 * PresentationSlideSubmissionController_PresentationRequest constructor.
	 * @param PresentationSlideSubmissionController $parent
	 * @param Presentation $presentation
     */
	public function __construct(PresentationSlideSubmissionController $parent, Presentation $presentation)
	{
		$this->parent = $parent;
		$this->presentation = $presentation;

		parent::__construct();
	}

	/**
	 * @param null $action
	 * @return String
     */
	public function Link($action = null)
	{
		return Controller::join_links(
			$this->parent->Link(),
			'presentation',
			$this->presentation->ID,
			$action
		);
	}

	/**
	 * @return Presentation
     */
	public function getPresentation()
	{
		return $this->presentation;
	}

	/**
	 * @return mixed
     */
	public function Form()
	{
		$form = PresentationMediaUploadForm::create(
			$this,
			'Form', 
			$this->presentation
		);

		return $form;
	}


	/**
	 * @return mixed
     */
	public function LinkToForm()
	{
		$form = PresentationLinkToForm::create(
			$this, 
			'LinkToForm', 
			$this->presentation
		);
		
		return $form;
	}

	/**
	 * @param SS_HTTPRequest $r
	 * @return mixed
     */
	public function upload(SS_HTTPRequest $r)
	{
		return $this->renderWith([
			'PresentationSlideSubmissionController_upload',
			'Page'
		]);
	}


	/**
	 * @param SS_HTTPRequest $r
	 * @return mixed
     */
	public function linkto(SS_HTTPRequest $r)
	{
		return $this->renderWith([
			'PresentationSlideSubmissionController_linkto',
			'Page'
		]);
	}


	/**
	 * @param SS_HTTPRequest $r
	 * @return mixed
     */
	public function success(SS_HTTPRequest $r)
	{
		if(!SecurityToken::inst()->check($r->getVar('key'))) {
			$this->httpError(404);
		}

		$material = PresentationSlide::get()->byID($r->getVar('material'));

		if(!$material) {
			$this->httpError(404);
		}

		return $this->customise([
			'Material' => $material,
			'Presentation' => $this->presentation
		])->renderWith([
			'PresentationSlideSubmissionController_success',
			'Page'
		]);
	}
}