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


	public function presentations(SS_HTTPRequest $request)
	{

        try {

            $token = Session::get('PresentationSlideSubmission.Token');

            if (isset($_REQUEST['t'])) {
                $token = base64_decode($_REQUEST['t']);
                Session::set('PresentationSlideSubmission.Token', $token);
                return $this->redirect(Director::absoluteURL('submit-slides/presentations'));
            }

            if(empty($token))
                throw new InvalidArgumentException('missing token!');

            $email = PresentationSpeakerUploadPresentationMaterialEmail::get()
                ->filter('Hash', PresentationSpeakerUploadPresentationMaterialEmail::HashConfirmationToken($token))
                ->first();


            if (is_null($email)) {
                throw new NotFoundEntityException('PresentationSpeakerUploadPresentationMaterialEmail','');
            }

            if(!$email->alreadyRedeemed()) {
                $email->redeem($token);
                $email->write();
            }

            $presentations = $email->Speaker()->PublishedPresentations($email->Summit()->ID);

            // No presentations
            if (!$presentations || $presentations->count() == 0) {
                $this->clearSessionState();
                return $this->httpError(404, 'Sorry, it does not appear that you have any presentations.');
            }

            Session::set('PresentationSlideSubmission.SpeakerID', $email->Speaker()->ID);
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

            $data['Speaker']       = $email->Speaker();
            $data['Presentations'] = $presentations;
            return $this->customise($data);
        }
        catch(Exception $ex)
        {
            SS_Log::log($ex->getMessage(), SS_Log::WARN);
            $this->clearSessionState();
            return $this->httpError(404, 'Sorry, this speaker upload slide token does not seem to be correct.');
        }
	}

	private function clearSessionState(){
        Session::clear("PresentationSlideSubmission.Token");
        Session::clear("PresentationSlideSubmission.SpeakerID");
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
		$speakerID = Session::get('PresentationSlideSubmission.SpeakerID');

		if ($presentation && $speakerID && $presentation->Speakers()->find('ID', $speakerID)) {
			$request = PresentationSlideSubmissionController_PresentationRequest::create($this, $presentation);

			return $request->handleRequest($r, DataModel::inst());
		}

		return $this->customise([
			'HasError' => true
		]);
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


	public function success(SS_HTTPRequest $request)
	{
	    // check security token generate on
        // summit/code/forms/PresentationLinkToForm.php Line 92
        // @link PresentationLinkToForm::saveLink
		if(!SecurityToken::inst()->check($request->getVar('key'))) {
			$this->httpError(404);
		}

		$material = PresentationSlide::get()->byID($request->getVar('material'));

		if(!$material) {
			$this->httpError(404);
		}

		return $this->customise([
			'Material'     => $material,
			'Presentation' => $this->presentation
		])->renderWith([
			'PresentationSlideSubmissionController_success',
			'Page'
		]);
	}
}