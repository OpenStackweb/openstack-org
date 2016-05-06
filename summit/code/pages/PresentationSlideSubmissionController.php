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
	private static $allowed_actions = [
		'presentations',
		'handlePresentation',
		'emailspeakers' => 'ADMIN'
	];

	private static $url_handlers = [
		'presentation/$ID' => 'handlePresentation'
	];

	public function init()
	{
		parent::init();

		// sanity check
		if(!Summit::get_most_recent()) {
			return $this->httpError(404, 'There is no recent summit');
		}
	}

	public function Link($action = null)
	{
		return Controller::join_links(
			BASE_URL,
			'submit-slides'
		);
	}

	public function presentations(SS_HTTPRequest $r)
	{
		$data = [];
		$speaker = null;

		if ($r->getVar('key')) {
			$key = Convert::raw2sql($_GET['key']);
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
				return $this->redirect($this->Link() . 'upload/' . $presentationID);			
			}
		}

		$data['Speaker'] = $speaker;
		$data['Presentations'] = $presentations;

		return $this->customise($data);
	}

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

	public function emailspeakers(SS_HTTPRequest $r)
	{
		$getVars = $r->getVars();
		$speakers = PresentationSpeaker::get();
		$summit = Summit::get_most_recent();
		foreach ($speakers as $speaker) {			
			$presentations = $speaker->PublishedPresentations($summit->ID);
				// Todo -- how to deal with this?
				// !$speaker->GeneralOrKeynote() &&
				// !SchedSpeakerEmailLog::BeenEmailed($Speaker->email) &&
			if ($presentations->exists() && self::valid_email($speaker->Member()->Email)) {
				$to = $speaker->Member()->Email;				
				$subject = "Important Speaker Information for OpenStack Summit in {$summit->Title}";

				$email = EmailFactory::getInstance()->buildEmail('do-not-reply@openstack.org', $to, $subject);
				$email->setTemplate("UploadPresentationSlidesEmail");
				$email->populateTemplate($speaker);

				if (isset($getVars['confirm'])) {
					//SchedSpeakerEmailLog::addSpeaker($to);
					$email->send();
				} else {					
					echo $email->debug();
				}

				echo 'Email sent to ' . $to . '<br/>';
			}
		}
	}


	/**
	 * Validate an email address.
	 * Provide email address (raw input)
	 * Returns true if the email address has the email
	 * address format and the domain exists.
	 */
	public static function valid_email($email)
	{
		$isValid = true;
		$atIndex = strrpos($email, "@");
		if (is_bool($atIndex) && !$atIndex) {
			$isValid = false;
		} else {
			$domain = substr($email, $atIndex + 1);
			$local = substr($email, 0, $atIndex);
			$localLen = strlen($local);
			$domainLen = strlen($domain);
			if ($localLen < 1 || $localLen > 64) {
				// local part length exceeded
				$isValid = false;
			} else if ($domainLen < 1 || $domainLen > 255) {
				// domain part length exceeded
				$isValid = false;
			} else if ($local[0] == '.' || $local[$localLen - 1] == '.') {
				// local part starts or ends with '.'
				$isValid = false;
			} else if (preg_match('/\\.\\./', $local)) {
				// local part has two consecutive dots
				$isValid = false;
			} else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain)) {
				// character not valid in domain part
				$isValid = false;
			} else if (preg_match('/\\.\\./', $domain)) {
				// domain part has two consecutive dots
				$isValid = false;
			} else if
			(!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/',
				str_replace("\\\\", "", $local))
			) {
				// character not valid in local part unless
				// local part is quoted
				if (!preg_match('/^"(\\\\"|[^"])+"$/',
					str_replace("\\\\", "", $local))
				) {
					$isValid = false;
				}
			}
			if ($isValid && !(checkdnsrr($domain, "MX") || checkdnsrr($domain, "A"))) {
				// domain not found in DNS
				$isValid = false;
			}
		}
		return $isValid;
	}

}

class PresentationSlideSubmissionController_PresentationRequest extends Controller
{
	private static $allowed_actions = [
		'upload',
		'Form',
		'LinkToForm',
		'linkto',
		'success'
	];

	protected $parent;

	protected $presentation;

	public function __construct(PresentationSlideSubmission $parent, Presentation $presentation)
	{
		$this->parent = $parent;
		$this->presentation = $presentation;

		parent::__construct();
	}

	public function Link($action = null) 
	{
		return Controller::join_links(
			$this->parent->Link(),
			'presentation',
			$this->presentation->ID,
			$action
		);
	}

	public function getPresentation() 
	{
		return $this->presentation;
	}

	public function Form()
	{
		$form = PresentationMediaUploadForm::create(
			$this,
			'Form', 
			$this->presentation
		);

		return $form;
	}


	public function LinkToForm()
	{
		$form = PresentationLinkToForm::create(
			$this, 
			'LinkToForm', 
			$this->presentation
		);
		
		return $form;
	}

	public function upload(SS_HTTPRequest $r) 
	{
		return $this->renderWith([
			'PresentationSlideSubmissionController_upload',
			'Page'
		]);
	}


	public function linkto(SS_HTTPRequest $r) 
	{
		return $this->renderWith([
			'PresentationSlideSubmissionController_linkto',
			'Page'
		]);
	}


	public function success(SS_HTTPRequest $r)
	{
		if(!SecurityToken::inst()->check($r->getVar('key'))) {
			return $this->httpError(404);
		}

		$material = PresentationSlide::get()->byID($r->getVar('material'));

		if(!$material) {
			return $this->httpError(404);
		}

		return $this->customise([
			'Material' => $material,
			'Presentation' => $this->presentation
		])->renderWith([
			'PresentationSlideSubmissionController_success',
			'Page'
		]);

		return $this->redirect($this->Link('presentations'));
	}
}