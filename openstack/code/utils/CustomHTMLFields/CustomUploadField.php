<?php

/**
 * Class CustomUploadField
 */
final class CustomUploadField extends UploadField {

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
} 