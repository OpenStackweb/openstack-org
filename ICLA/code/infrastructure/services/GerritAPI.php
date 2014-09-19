<?php

/**
 * Class GerritAPI
 */
final class GerritAPI
	extends AbstractRestfulServiceRequestor
	implements IGerritAPI  {

	/**
	 * @var string
	 */
	private $user;

	/**
	 * @var string
	 */
	private $password;

	/**
	 * @var string
	 */
	private $domain;

	/**
	 * @param string $domain
	 * @param string $user
	 * @param string $password
	 */
	public function __construct($domain, $user, $password){
		$this->domain   = $domain;
		$this->user     = $user;
		$this->password = $password;
	}

	/**
	 * @param string $group_id
	 * @return array|mixed
	 * @throws UnauthorizedRestfullAPIException
	 */
	public function listAllMembersFromGroup($group_id){
		$url  = sprintf("%s/a/groups/%s/members", $this->domain, $group_id);
		$json = $this->fixGerritJsonResponse($this->doRequest($url, $this->user, $this->password));
		if(strtolower($json)==='unauthorized')
			throw new UnauthorizedRestfullAPIException($json);

		return json_decode($json, true);
	}

	/**
	 * @param string $gerrit_user_id
	 * @return DateTime
	 */
	public function getUserLastCommit($gerrit_user_id){
		$url  = sprintf("%s/changes/?q=status:merged+owner:%s&n=1", $this->domain, $gerrit_user_id);
		$json = $this->fixGerritJsonResponse($this->doRequest($url));
		$response = json_decode($json, true);
		if(is_array($response) && count($response) > 0){
			$response = $response[0];
			$last_committed_date = $response['updated'];
			$last_committed_date = explode('.',$last_committed_date);
			if(is_array($last_committed_date) && count($last_committed_date) > 0){
				return DateTime::createFromFormat('Y-m-d H:i:s', $last_committed_date[0]);
			}
		}
		return null;
	}

	private function fixGerritJsonResponse($json){
		$json = str_replace(")]}'",'',$json);
		return $json;
	}
}