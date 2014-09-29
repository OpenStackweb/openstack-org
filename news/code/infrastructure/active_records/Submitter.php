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
        'Phone' => 'Varchar',
	);

    /**
     * @var string
     */
    protected static $unique_identifier_field = 'Email';

	/**
	 * @return int
	 */
	public function getIdentifier()
	{
		return (int)$this->getField('ID');
	}
}