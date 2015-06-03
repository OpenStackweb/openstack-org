<?php


class Tokenable extends DataExtension {


	public function checkAuthToken(SS_HTTPRequest $r) {
		if($token = $r->requestVar('token')) {
			if($member = Member::get()->filter('AuthenticationToken', $token)->first()) {
				if($member->checkToken()) {
					$member->refreshToken();					
					$member->login();

					return true;
				}

				return $this->owner->httpError(403, "Token is expired");
			}

			return $this->owner->httpError(403, "Invalid token");
		}

		return false;
	}
}