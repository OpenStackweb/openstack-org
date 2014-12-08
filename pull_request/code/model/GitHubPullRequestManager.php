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

	const NON_MEMBER_REJECT_REASON            = 'NON_MEMBER_REJECT_REASON';

	const NON_FOUNDATION_MEMBER_REJECT_REASON = 'NON_FOUNDATION_MEMBER_REJECT_REASON';

	const NON_SIGNED_CLA_REJECT_REASON        = 'NON_SIGNED_CLA_REJECT_REASON';

	/**
	 * @param array $pull_request_data
	 * @throws NonFoundationMemberException
	 * @throws NotFoundEntityException
	 * @throws NonSignedCLAFoundationMemberException
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
				if (!$member)
				{
					$this->rejectPullRequest($pull_request_data, self::NON_MEMBER_REJECT_REASON);
					throw new NotFoundEntityException('Member',sprintf('user email %s does not exists!',$user_email));
				}
				if (!$member->isFoundationMember())
				{
					//reject pull request
					$this->rejectPullRequest($pull_request_data, self::NON_FOUNDATION_MEMBER_REJECT_REASON);
					throw new NonFoundationMemberException($user_email);
				}
				if (!$member->hasSignedCLA())
				{
					//reject pull request
					$this->rejectPullRequest($pull_request_data, self::NON_SIGNED_CLA_REJECT_REASON);
					throw new NonSignedCLAFoundationMemberException($user_email);
				}
			}
		}
	}

	/**
	 * @param array  $pull_request_data
	 * @param string $reject_reason
	 */
	private function rejectPullRequest(array $pull_request_data, $reject_reason){
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
		$comment_body = '';
		switch($reject_reason){
			case self::NON_MEMBER_REJECT_REASON:
			case self::NON_FOUNDATION_MEMBER_REJECT_REASON:{
				$comment_body = sprintf('User %s is not an OpenStack Foundation Member. Please go to https://www.openstack.org/join/register/ and complete the registration form in order to become a Foundation Member. After that, please take a look at How to Contribute to Openstack (https://wiki.openstack.org/wiki/How_To_Contribute) and make sure you sign the Contributer License Agreement (http://docs.openstack.org/infra/manual/developers.html#account-setup).', $user['login']);
			}
			break;
			case self::NON_SIGNED_CLA_REJECT_REASON:
			{
				$comment_body = sprintf('User %s has not signed the Contributer License Agreement. Please take a look at How to Contribute to Openstack (https://wiki.openstack.org/wiki/How_To_Contribute) and make sure you sign the CLA (http://docs.openstack.org/infra/manual/developers.html#account-setup).', $user['login']);
			}
			break;
		}
		$params['body'] = $comment_body;
		$client->api('issue')->comments()->create($owner['login'], $repo['name'], $id ,  $params);
	}
} 