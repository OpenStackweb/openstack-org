<?php

/**
 * Class EntityAlreadyExistsException
 */
class EntityAlreadyExistsException extends Exception {

	public function __construct($class_name,$criteria){
		$message = sprintf('Entity %s %s already exists!.',$class_name,$criteria);
		parent::__construct($message);
	}
}