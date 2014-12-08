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
 * Class GitHubPullRequestManager
 * validates if a pull request belongs to a foundation member or not
 */
class GitHubPullRequestManager {

	/**
	 * @param array $pull_request_data
	 * @throws NonFoundationMemberException
	 * @throws NotFoundEntityException
	 */
	public function validatePullRequest(array $pull_request_data){
		//get sender user
		if(isset($pull_request_data['user']) && $sender = $pull_request_data['user']) {
			$user_name = $sender['login'];
			//get user email from api
			// https://api.github.com/users/:user_name
			$client = new \Github\Client();
			$user   = $client->api('user')->show($user_name);
			if ($user != null && isset($user['email']) && $user_email = $user['email']) {
				//check if exist on db
				$member = Member::get()->filter('Email', $user_email)->first();
				if (!$member) {
					$this->rejectPullRequest($pull_request_data);
					throw new NotFoundEntityException('Member',sprintf('user email %s does not exists!',$user_email));
				}
				if (!$member->isFoundationMember()) {
					//reject pull request
					$this->rejectPullRequest($pull_request_data);
					throw new NonFoundationMemberException($user_email);
				}
			}
		}
	}

	/**
	 * @param array $pull_request_data
	 * @return void
	 */
	private function rejectPullRequest(array $pull_request_data){
		$client = new \Github\Client();
		$params = array();
		$params['state'] = 'closed';
		$base  = $pull_request_data['base'];
		$head  = $pull_request_data['head'];
		$user  = $head['user'];
		$repo  = $base['repo'];
		$owner = $repo['owner'];
		$id    = $pull_request_data['number'];
		if(!defined('GITHUB_API_OAUTH2TOKEN'))
			throw new InvalidArgumentException();
		$client->authenticate(GITHUB_API_OAUTH2TOKEN, null, \Github\Client::AUTH_HTTP_TOKEN);
		$client->api('pull_request')->update($owner['login'], $repo['name'], $id ,  $params);
		$params = array();
		$params['body'] = sprintf('user %s is not a Foundation Member, please join to %s', $user['login'], 'https://www.openstack.org/join/register/');
		$client->api('issue')->comments()->create($owner['login'], $repo['name'], $id ,  $params);
	}
} 