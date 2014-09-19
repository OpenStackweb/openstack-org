<?php

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

		$foundation_member_repository = $this->foundation_member_repository;
		$notification_factory         = $this->notification_factory;
		$notification_repository      = $this->notification_repository;
		$election_repository          = $this->election_repository;

		return $this->tx_manager->transaction(function() use($max_past_elections, $batch_size, $sender, $foundation_member_repository,$notification_repository, $election_repository,  $notification_factory){
			$res= $foundation_member_repository->getMembersThatNotVotedOnLatestNElections($max_past_elections, $batch_size, 0, $election_repository);
			$last_election = $election_repository->getLatestNElections(1);
			$last_election = $last_election[0];
			foreach($res as $foundation_member_id){
				$foundation_member = $foundation_member_repository->getById($foundation_member_id);
				$notification      = $notification_factory->build($foundation_member, $last_election);
				$sender->send($foundation_member, $notification, $notification_repository);
				$notification_repository->add($notification);
			}
			return count($res);
		});
	}

	/**
	 * @param int $batch_size
	 */
	public function revokeIgnoredNotifications($batch_size){
		$notification_repository = $this->notification_repository;
		$this->tx_manager->transaction(function() use($batch_size, $notification_repository){
			$notifications = $notification_repository->getNotificationsSentXDaysAgo(IFoundationMemberRevocationNotification::DaysBeforeRevocation, $batch_size);
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
		$election_repository  = $this->election_repository;
		$this->tx_manager->transaction(function() use($notification , $election_repository){
			$latest_election  = $election_repository->getLatestNElections(1);
			$latest_election  = $latest_election[0];
			$notification->renew($latest_election);
		});
	}

	/**
	 * @param IFoundationMemberRevocationNotification $notification
	 */
	public function deleteAccount(IFoundationMemberRevocationNotification $notification){
		$foundation_member_repository = $this->foundation_member_repository;
		$this->tx_manager->transaction(function() use($notification, $foundation_member_repository){
			$notification->resign();
			$foundation_member = $notification->recipient();
			$foundation_member->logOut();
			$foundation_member_repository->delete($foundation_member);
		});
	}
} 