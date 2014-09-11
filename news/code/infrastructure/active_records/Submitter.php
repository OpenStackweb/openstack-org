<?php

/**
 * Class Submitter
 */
final class Submitter extends DataObject implements ISubmitter {

	static $create_table_options = array('MySQLDatabase' => 'ENGINE=InnoDB');

	static $db = array(
		'FirstName'  => 'Varchar',
        'LastName'  => 'Varchar',
		'Email' => 'Varchar',
        'Company' => 'Varchar',
        'Phone' => 'Int',
	);

	/**
	 * @return int
	 */
	public function getIdentifier()
	{
		return (int)$this->getField('ID');
	}
}