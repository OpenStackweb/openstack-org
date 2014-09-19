<?php

/**
 * Class BatchTask
 */
final class BatchTask
	extends DataObject
	implements IBatchTask {

	static $create_table_options = array('MySQLDatabase' => 'ENGINE=InnoDB');

	static $db = array(
		'Name'             => 'Text',
		'LastResponse'     => 'Text',
		'LastRecordIndex'  => 'Int',
		'LastResponseDate' => 'SS_Datetime',
		'TotalRecords'     => 'Int',
	);

	/***
	 * @return string
	 */
	public function name()
	{
		return (string)$this->getField('Name');
	}

	/***
	 * @return int
	 */
	public function lastRecordProcessed()
	{
		return (int)$this->getField('LastRecordIndex');
	}

	/**
	 * @return string
	 */
	public function lastResponse()
	{
		return (string)$this->getField('LastResponse');
	}

	/**
	 * @return DateTime
	 */
	public function lastResponseDate()
	{
		return new DateTime($this->getField('LastResponseDate'));
	}

	/**
	 * @return int
	 */
	public function totalRecords()
	{
		return (int)$this->getField('TotalRecords');
	}

	/**
	 * @param string $response
	 * @return void
	 */
	public function updateResponse($response)
	{
		$this->setField('LastResponse', $response);
	}

	/**
	 * @return void
	 */
	public function updateLastRecord()
	{
		$last = $this->lastRecordProcessed();
		$this->setField('LastRecordIndex', $last+1 );
	}

	/**
	 * @return int
	 */
	public function getIdentifier()
	{
		return (int)$this->getField('ID');
	}

	/**
	 * @param int $total_qty
	 * @return void
	 */
	public function initialize($total_qty){
		$this->setField('LastRecordIndex', 0 );
		$this->setField('TotalRecords', $total_qty );
	}
}