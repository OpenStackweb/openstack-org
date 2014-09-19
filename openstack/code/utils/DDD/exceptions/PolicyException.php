<?php

/**
 * Class PolicyException
 */
class PolicyException extends Exception {

	/**
	 * @param string $policy_name
	 * @param string $condition
	 */
	public function __construct($policy_name, $condition){
		$message = sprintf('Policy %s not fulfilled (%s) !.',$policy_name, $condition);
		parent::__construct($message);
	}
}