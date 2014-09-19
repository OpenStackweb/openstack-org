<?php

/**
 * Class RevocationNotificationEmailSender
 */
final class RevocationNotificationEmailSender implements IRevocationNotificationSender  {


	/**
	 * @param IFoundationMember                                 $foundation_member
	 * @param IFoundationMemberRevocationNotification           $notification
	 * @param IFoundationMemberRevocationNotificationRepository $notification_repository
	 */
	public function send(IFoundationMember $foundation_member,
	                     IFoundationMemberRevocationNotification $notification,
	                     IFoundationMemberRevocationNotificationRepository $notification_repository)
	{
		$email = EmailFactory::getInstance()->buildEmail(REVOCATION_NOTIFICATION_EMAIL_FROM,
			$foundation_member->Email,
			REVOCATION_NOTIFICATION_EMAIL_SUBJECT);

		$email->setTemplate('RevocationNotificationEmail');

		do{
			$hash = $notification->generateHash();
		} while ($notification_repository->existsHash($hash));
		$link = sprintf('%s/revocation-notifications/%s/action', Director::protocolAndHost(), $hash);
		$email->populateTemplate(array(
			'TakeActionLink' => $link,
			'EmailFrom'      => REVOCATION_NOTIFICATION_EMAIL_FROM,
			'ExpirationDate' => $notification->expirationDate()->format('F j')
		));

		$email->send();
	}
}