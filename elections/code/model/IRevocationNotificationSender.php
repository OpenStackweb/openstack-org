<?php

/**
 * Interface IRevocationNotificationSender
 */
interface IRevocationNotificationSender {
	/**
	 * @param IFoundationMember                                 $foundation_member
	 * @param IFoundationMemberRevocationNotification           $notification
	 * @param IFoundationMemberRevocationNotificationRepository $notification_repository
	 * @return void
	 */
	public function send(IFoundationMember $foundation_member,
	                     IFoundationMemberRevocationNotification $notification,
	                     IFoundationMemberRevocationNotificationRepository $notification_repository);
} 