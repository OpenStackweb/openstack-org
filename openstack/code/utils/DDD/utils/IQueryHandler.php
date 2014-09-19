<?php

/**
 * Interface IQueryHandler
 */
interface  IQueryHandler {
	/**
	 * @param IQuerySpecification $specification
	 * @return IQueryResult
	 */
	public function handle(IQuerySpecification $specification);
} 