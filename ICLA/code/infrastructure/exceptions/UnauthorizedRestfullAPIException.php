<?php
/**
 * Class UnauthorizedRestfullAPIException
 */
final class UnauthorizedRestfullAPIException extends Exception {

	public function __construct($message){
		parent::__construct($message);
	}

} 