<?php


class PresentationAPITokenGenerator extends Controller {


	public function index(SS_HTTPRequest $r) {
		$username = $r->postVar('username');
		$password = $r->postVar('password');

		if(!$username || !$password) {
			return $this->httpError(400, "You must provide 'username' and 'password' parameters in the request");
		}

		if($member = Member::get()->filter('Email', $username)->first()) {
			if($member->checkPassword($password)) {
				$member->assignToken();
				$member->write();
				$response = new SS_HTTPResponse(200);
				$response
					->addHeader('Content-type', 'application/json')
					->setBody(Convert::array2json(array(
						'token' => $member->AuthenticationToken
					)));

				return $response;
			}			
		}

		return $this->httpError(403, "Invalid login");
	}
}