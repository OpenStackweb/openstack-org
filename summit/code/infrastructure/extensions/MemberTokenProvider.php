<?php

/**
 * Adds passwordless authentication to members via unique tokens
 *
 * @author Uncle Cheese <unclecheese@leftandmain.com>
 */
class MemberTokenProvider extends DataExtension {


	private static $db = array (
		'AuthenticationToken' => 'Varchar(128)',
		'AuthenticationTokenExpire' => 'Int'
	);


	private static $indexes = array (
		'AuthenticationToken' => true
	);


	/**
	 * Checks if the token is expired
	 * @return boolean
	 */
	public function checkToken() {
		return time() < $this->owner->AuthenticationTokenExpire;
	}


	/**
	 * Sets the expire time of the token to a given number of seconds in the future
	 * @param int $period number of seconds
	 */
	public function setTokenExpiry($period = null) {
		if(!$period) {
			$period = (int) Config::inst()->get("MemberTokenProvider", "token_expire");
		}
		$this->owner->AuthenticationTokenExpire	= time()+$period;
	}


	/**
	 * Assigns a new token to the member	 
	 */
	public function assignToken() {
		$token = $this->generateToken();
		while (
			Member::get()->filter('AuthenticationToken', $token)
						 ->exclude('ID', $this->owner->ID)
						 ->exists()
		) {
			$token = $this->generateToken();
		}

		$this->owner->AuthenticationToken = $token;
		$this->setTokenExpiry();	
	}


	/**
	 * Creates a 64 character token
	 * @return string
	 */
	protected function generateToken() {
		return md5(uniqid().time()).sha1(time().uniqid());
	}


	/**
	 * Ensures all members have tokens
	 */
    public function onBeforeWrite() {
        if(!$this->owner->AuthenticationToken) {
            $this->assignToken();
        }
    }
}