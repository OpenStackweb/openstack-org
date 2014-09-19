<?php

/**
 * Class NotFoundEntityException
 */
class NotFoundEntityException extends Exception {

	public function __construct($class_name,$criteria){
		$message = sprintf('Entity %s by %s not found!.',$class_name,$criteria);
		parent::__construct($message);
	}
}