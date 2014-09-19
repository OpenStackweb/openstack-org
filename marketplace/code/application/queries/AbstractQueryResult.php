<?php

/**
 * Class AbstractQueryResult
 */
class AbstractQueryResult implements IQueryResult {
	/**
	 * @param array $result
	 */
	public function __construct(array $result){
		$this->result = $result;
	}

	/**
	 * @var array
	 */
	private $result;
	/**
	 * @return array
	 */
	public function getResult()
	{
		return  $this->result;
	}
} 