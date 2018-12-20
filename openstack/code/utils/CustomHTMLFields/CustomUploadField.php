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
 * Class CustomUploadField
 */
final class CustomUploadField extends CloudAwareUploadField {

	/**
	 * @var array
	 */
	private static $allowed_actions = array(
		'upload',
		'attach',
		'handleItem',
		'handleSelect',
		'fileexists'
	);

	/**
	 * @var array
	 */
	private static $url_handlers = array(
		'item/$ID' => 'handleItem',
		'select' => 'handleSelect',
		'$Action!' => '$Action',
	);

	/**
	 * @var string
	 */
	private $record_class;
	/**
	 * Action to handle upload of a single file
	 *
	 * @param SS_HTTPRequest $request
	 * @return SS_HTTPResponse
	 * @return SS_HTTPResponse
	 */
	public function upload(SS_HTTPRequest $request) {
		if($this->isDisabled() || $this->isReadonly() || !$this->canUpload()) {
			return $this->httpError(403);
		}

		// Protect against CSRF on destructive action
		$token = $this->getForm()->getSecurityToken();
		if(!$token->checkRequest($request)) return $this->httpError(400);

		// Get form details
		$name = $this->getName();
		$postVars = $request->postVar($name);

		// Save the temporary file into a File object
		$uploadedFiles = $this->extractUploadedFileData($postVars);
		$firstFile = reset($uploadedFiles);
		$file = $this->saveTemporaryFile($firstFile, $error);
		if(empty($file)) {
			$return = array('error' => $error);
		} else {
			$return = $this->encodeFileAttributes($file);
		}

		// Format response with json
		$response = new SS_HTTPResponse(Convert::raw2json(array($return)));
		$response->addHeader('Content-Type', 'text/plain');
		if (!empty($return['error'])) $response->setStatusCode(200);
		return $response;
	}

	/**
	 * Gets the foreign class that needs to be created, or 'File' as default if there
	 * is no relationship, or it cannot be determined.
	 *
	 * @param $default Default value to return if no value could be calculated
	 * @return string Foreign class name.
	 */
	public function getRelationAutosetClass($default = 'File') {
		if(empty($this->record_class))
			return parent::getRelationAutosetClass($default);
		return $this->record_class;
	}

	public function setRecordClass($record_class){
		$this->record_class = $record_class;
	}

	public function Field($properties = array()) {
		$res = parent::Field($properties);
		Requirements::css('themes/openstack/css/custom.uploadfield.css');
		Requirements::javascript('themes/openstack/javascript/custom.uploadfield.js');
		return $res;
	}

    /**
     * @param SS_HTTPRequest $request
     * @return UploadField_ItemHandler
     */
    public function handleSelect(SS_HTTPRequest $request) {
        return CustomUploadField_SelectHandler::create($this, $this->getFolderName());
    }
}

class CustomUploadField_SelectHandler extends UploadField_SelectHandler {

    public function index() {
        Requirements::javascript(THIRDPARTY_DIR . '/jquery-entwine/dist/jquery.entwine-dist.js');
        return parent::index();
    }

    /**
     * Build the file selection form.
     *
     * @return Form
     */
    public function Form() {
        // Find out the requested folder ID.
        $folderID = $this->parent->getRequest()->requestVar('ParentID');
        if (!isset($folderID)) {
            $folder = Folder::find_or_make($this->folderName);
            $folderID = $folder ? $folder->ID : 0;
        }

        // Construct the form
        $action = new FormAction('doAttach', _t('UploadField.AttachFile', 'Attach file(s)'));
        $action->addExtraClass('ss-ui-action-constructive icon-accept');
        $form = new Form(
            $this,
            'Form',
            new FieldList($this->getListField($folderID)),
            new FieldList($action)
        );

        // Add a class so we can reach the form from the frontend.
        $form->addExtraClass('uploadfield-form');

        return $form;
    }

}