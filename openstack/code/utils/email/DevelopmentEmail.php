<?php

/**
 * Class DevelopmentEmail
 * Sends Fake Emails replacing original to/cc/bcc with admin email
 */
final class DevelopmentEmail extends Email {

	/**
	 * Send the email in plaintext.
	 *
	 * @see send() for sending emails with HTML content.
	 * @uses Mailer->sendPlain()
	 *
	 * @param string $messageID Optional message ID so the message can be identified in bounces etc.
	 * @return bool Success of the sending operation from an MTA perspective.
	 * Doesn't actually give any indication if the mail has been delivered to the recipient properly)
	 */
	function sendPlain($messageID = null) {
		Requirements::clear();

		$this->parseVariables(true);

		if(empty($this->from)) $this->from = Email::getAdminEmail();

		$headers = $this->customHeaders;

		$headers['X-SilverStripeBounceURL'] = $this->bounceHandlerURL;

		if($messageID) $headers['X-SilverStripeMessageID'] = project() . '.' . $messageID;

		if(project()) $headers['X-SilverStripeSite'] = project();

		$to = defined('DEV_EMAIL_TO')? DEV_EMAIL_TO :Email::getAdminEmail();

		$subject = $this->subject;


		Requirements::restore();

		return self::mailer()->sendPlain($to, $this->from, $subject, $this->body, $this->attachments, $headers);
	}

	/**
	 * Send an email with HTML content.
	 *
	 * @see sendPlain() for sending plaintext emails only.
	 * @uses Mailer->sendHTML()
	 *
	 * @param string $messageID Optional message ID so the message can be identified in bounces etc.
	 * @return bool Success of the sending operation from an MTA perspective.
	 * Doesn't actually give any indication if the mail has been delivered to the recipient properly)
	 */
	public function send($messageID = null) {

		Requirements::clear();

		$this->parseVariables();

		if(empty($this->from)) $this->from = Email::getAdminEmail();

		$headers = $this->customHeaders;

		$headers['X-SilverStripeBounceURL'] = $this->bounceHandlerURL;

		if($messageID) $headers['X-SilverStripeMessageID'] = project() . '.' . $messageID;

		if(project()) $headers['X-SilverStripeSite'] = project();

		$to = defined('DEV_EMAIL_TO')? DEV_EMAIL_TO:Email::getAdminEmail();

		$this->to = $to;

		$subject = $this->subject;

		Requirements::restore();

		return self::mailer()->sendHTML($to, $this->from, $subject, $this->body, $this->attachments, $headers, $this->plaintext_body);
	}
} 