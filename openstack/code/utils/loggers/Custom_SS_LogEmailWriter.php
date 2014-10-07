<?php

/**
 * Class Custom_SS_LogEmailWriter
 */
final class Custom_SS_LogEmailWriter extends SS_LogEmailWriter {


	/**
	 * @config
	 * @var $send_from Email address to send log information from
	 */
	private static $send_from = 'errors@silverstripe.com';

	function __construct($emailAddress, $customSmtpServer = false)
	{
		parent::__construct($emailAddress, $customSmtpServer);
	}


	/**
	 * Send an email to the email address set in
	 * this writer.
	 */
	public function _write($event) {
		// If no formatter set up, use the default
		if(!$this->_formatter) {
			$formatter = new SS_LogErrorEmailFormatter();
			$this->setFormatter($formatter);
		}

		$formattedData = $this->_formatter->format($event);
		$subject       = $formattedData['subject'];
		$body          = $formattedData['data'];
		$email         = EmailFactory::getInstance()->buildEmail(self::$send_from, $this->emailAddress,$subject, $body);

		$email->send();
	}

} 