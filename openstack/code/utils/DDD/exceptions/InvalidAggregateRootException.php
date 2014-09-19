<?php

/**
 * Class InvalidAggregateRootException
 */
class InvalidAggregateRootException extends Exception {

	public function __construct($aggregate_root_name, $aggregate_root_id, $aggregate_name, $aggregate_id){
		$message = sprintf('Entity %s (%s) does not belongs to Entity %s (%s)'
			, $aggregate_name
			, $aggregate_id
			, $aggregate_root_name
			, $aggregate_root_id);
		parent::__construct($message);
	}
}