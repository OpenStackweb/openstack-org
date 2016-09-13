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
 * Class ICLAManager
 */
final class ICLAManager {

	/**
	 * @var IGerritAPI
	 */
	private $gerrit_api;

	/**
	 * @var IGerritUserRepository
	 */
	private $member_repository;

	/**
	 * @var ITransactionManager
	 */
	private $tx_manager;


	/**
	 * @param IGerritAPI           $gerrit_api
	 * @param IGerritUserRepository $member_repository
	 * @param ITransactionManager  $tx_manager
	 */
	public function __construct(IGerritAPI $gerrit_api,
                                IGerritUserRepository $member_repository,
								ITransactionManager  $tx_manager){

		$this->gerrit_api         = $gerrit_api;
		$this->member_repository  = $member_repository;
		$this->tx_manager         = $tx_manager;
	}

	/**
	 * @param string $icla_group_id
	 * @return int
	 */
	public function processICLAGroup($icla_group_id){


		$member_repository  = $this->member_repository;
		$gerrit_api         = $this->gerrit_api;


		return $this->tx_manager->transaction(function() use($icla_group_id, $member_repository,  $gerrit_api) {

			// query gerrit service
			$icla_members_response = $gerrit_api->listAllMembersFromGroup($icla_group_id);
			$icla_members_count    = count($icla_members_response);

			if($icla_members_count === 0) return; //nothing to process...


			$gerrit_users           = 0;
         	foreach($icla_members_response as $gerrit_info){

				$email       = @$gerrit_info['email'];
				$account_id  = @$gerrit_info['_account_id'];

				if(!empty($email) && !empty($account_id) ){
						$member = $member_repository->findByEmail($email);
						if($member){
							if(is_null($member->addGerritUser($account_id, $email))) continue;
                            ++$gerrit_users;
						}
						else{
						    // we dont have a member associated with it :/
                            // check by account id
                            if(!is_null($member_repository->getGerritUserByAccountId($account_id))) continue;
                            $gerrit_user             = new GerritUser();
                            $gerrit_user->AccountID  = $account_id;
                            $gerrit_user->Email      = $email;
                            $gerrit_user->write();
                            ++$gerrit_users;
                        }

				}
			}

			return $gerrit_users;
		});
	}
}