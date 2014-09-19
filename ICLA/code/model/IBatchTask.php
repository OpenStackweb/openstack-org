<?php
/***
 * Interface IBatchTask
 */
interface IBatchTask extends IEntity {
	/***
	 * @return string
	 */
	public function name();
	/***
	 * @return int
	 */
	public function lastRecordProcessed();

	/**
	 * @return string
	 */
	public function lastResponse();

	/**
	 * @return DateTime
	 */
	public function lastResponseDate();

	/**
	 * @return int
	 */
	public function totalRecords();

	/**
	 * @param string $response
	 * @return void
	 */
	public function updateResponse($response);

	/**
	 * @return void
	 */
	public function updateLastRecord();

	/**
	 * @param int $total_qty
	 * @return void
	 */
	public function initialize($total_qty);

} 