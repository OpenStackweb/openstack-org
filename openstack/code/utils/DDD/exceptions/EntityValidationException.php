<?php
/**
 * Class EntityValidationException
 */
class EntityValidationException extends Exception {

	/**
	 * @var array
	 */
	private $messages;

	public function __construct(array $messages){
		parent::__construct();
		$this->messages = $messages;
	}

	/**
	 * @return array
	 */
	public function getMessages(){
		return $this->messages;
	}
}