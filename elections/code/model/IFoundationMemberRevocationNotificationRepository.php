<?php

/**
 * Interface IFoundationMemberRevocationNotificationRepository
 */
interface IFoundationMemberRevocationNotificationRepository extends IEntityRepository {
	/**
	 * @param int $foundation_member_id
	 * @return IFoundationMemberRevocationNotification
	 */
	public function getByFoundationMember($foundation_member_id);

	/**
	 * @param int $days
	 * @param int $batch_size
	 * @return IFoundationMemberRevocationNotification[]
	 */
	public function getNotificationsSentXDaysAgo($days, $batch_size);

	/**
	 * @param string $hash
	 * @return bool
	 */
	public function existsHash($hash);

	/**
	 * @param string $hash
	 * @return IFoundationMemberRevocationNotification
	 */
	public function getByHash($hash);
} 