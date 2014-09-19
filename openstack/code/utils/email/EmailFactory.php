<?php

/**
 * Class EmailFactory
 * Utility class
 */
final class EmailFactory {

	/**
	 * @var EmailFactory
	 */
	private static $instance;

	private function __construct(){}

	private function __clone(){}

	/**
	 * @return EmailFactory
	 */
	public static function getInstance(){
		if(!is_object(self::$instance)){
			self::$instance = new EmailFactory();
		}
		return self::$instance;
	}

	/**
	 * @param null $from
	 * @param null $to
	 * @param null $subject
	 * @param null $body
	 * @param null $bounceHandlerURL
	 * @param null $cc
	 * @param null $bcc
	 * @return DevelopmentEmail|Email
	 */
	public function buildEmail($from = null, $to = null, $subject = null, $body = null, $bounceHandlerURL = null, $cc = null, $bcc = null){
		$env = 'dev';
		if(defined('SS_ENVIRONMENT_TYPE'))
			$env = SS_ENVIRONMENT_TYPE;
		return $env == 'dev'? new DevelopmentEmail($from, $to, $subject, $body, $bounceHandlerURL, $cc, $bcc) : new Email($from, $to, $subject, $body, $bounceHandlerURL, $cc, $bcc);
	}
} 