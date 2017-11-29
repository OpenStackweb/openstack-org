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
 * Class RevocationNotificationManager
 */
final class RevocationNotificationManager {

	/**
	 * @var IFoundationMemberRepository
	 */
	private $foundation_member_repository;

	/**
	 * @var IFoundationMemberRevocationNotificationRepository
	 */
	private $notification_repository;

	/**
	 * @var IElectionRepository
	 */
	private $election_repository;

	/**
	 * @var IFoundationMemberRevocationNotificationFactory
	 */
	private $notification_factory;
	/**
	 * @var ITransactionManager
	 */
	private $tx_manager;

	/**
	 * @param IFoundationMemberRepository                       $foundation_member_repository
	 * @param IFoundationMemberRevocationNotificationRepository $notification_repository
	 * @param IFoundationMemberRevocationNotificationFactory    $notification_factory
	 * @param IElectionRepository                               $election_repository
	 * @param ITransactionManager                               $tx_manager
	 */
	public function __construct(IFoundationMemberRepository $foundation_member_repository,
	                            IFoundationMemberRevocationNotificationRepository $notification_repository,
	                            IElectionRepository $election_repository,
	                            IFoundationMemberRevocationNotificationFactory $notification_factory,
						        ITransactionManager $tx_manager){

		$this->foundation_member_repository = $foundation_member_repository;
		$this->notification_repository      = $notification_repository;
		$this->election_repository          = $election_repository;
		$this->notification_factory         = $notification_factory;
		$this->tx_manager                   = $tx_manager;
	}

	/**
	 * @param int                           $max_past_elections
	 * @param int                           $batch_size
	 * @param IRevocationNotificationSender $sender
	 * @return int
	 */
	public function sendOutNotifications($max_past_elections, $batch_size, IRevocationNotificationSender $sender){

		return $this->tx_manager->transaction(function() use
        (
            $max_past_elections,
            $batch_size,
            $sender
        )
        {

            $res           = $this->foundation_member_repository->getMembersThatNotVotedOnLatestNElections
                            (
                                $max_past_elections,
                                $batch_size,
                                0,
                                $this->election_repository
                            );

			$last_election = $this->election_repository->getLatestNElections(1);
			$last_election = $last_election[0];

			foreach($res as $foundation_member_id){

				$foundation_member = $this->foundation_member_repository->getById($foundation_member_id);
				$notification      = $this->notification_factory->build($foundation_member, $last_election);

				$sender->send($foundation_member, $notification, $this->notification_repository);
				$notification->write();
			}
			return count($res);
		});
	}

	/**
	 * @param int $batch_size
	 */
	public function revokeIgnoredNotifications($batch_size){
		$this->tx_manager->transaction(function() use($batch_size){
			$notifications = $this->notification_repository->getNotificationsSentXDaysAgo
            (
                IFoundationMemberRevocationNotification::DaysBeforeRevocation,
                $batch_size
            );
			foreach($notifications as $notification){
				$notification->revoke();
			}
		});
	}

	/**
	 * @param IFoundationMemberRevocationNotification $notification
	 */
	public function revokeNotification(IFoundationMemberRevocationNotification $notification){
		$this->tx_manager->transaction(function() use($notification){
			$notification->revoke();
		});
	}

	/**
	 * @param IFoundationMemberRevocationNotification $notification
	 */
	public function renewNotification(IFoundationMemberRevocationNotification $notification){
		$this->tx_manager->transaction(function() use($notification){
			$latest_election  = $this->election_repository->getLatestNElections(1);
			$latest_election  = $latest_election[0];
			$notification->renew($latest_election);
		});
	}

	/**
	 * @param IFoundationMemberRevocationNotification $notification
	 */
	public function deleteAccount(IFoundationMemberRevocationNotification $notification){
		$this->tx_manager->transaction(function() use($notification){
			$notification->resign();
			$foundation_member = $notification->recipient();
			$foundation_member->logOut();
			$this->foundation_member_repository->delete($foundation_member);
		});
	}
} 