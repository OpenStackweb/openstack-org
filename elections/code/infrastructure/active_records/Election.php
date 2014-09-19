<?php

/**
 * Class Election
 */
final class Election extends DataObject implements IElection {

	static $create_table_options = array('MySQLDatabase' => 'ENGINE=InnoDB');

	static $db = array(
		'ElectionsOpen'  => 'Date', // The day elections start
		'ElectionsClose' => 'Date', // The day they close
	);

	/**
	 * @return DateTime
	 */
	public function startDate()
	{
		return new DateTime($this->getField('ElectionsOpen'));
	}

	/**
	 * @return DateTime
	 */
	public function endDate()
	{
		return new DateTime($this->getField('ElectionsClose'));
	}

	/**
	 * @return int
	 */
	public function getIdentifier()
	{
		return (int)$this->getField('ID');
	}
}