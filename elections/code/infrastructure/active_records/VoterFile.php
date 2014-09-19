<?php
/**
 * Class VoterFile
 */
final class VoterFile extends DataObject implements IVoterFile {

	static $create_table_options = array('MySQLDatabase' => 'ENGINE=InnoDB');

	static $db = array(
		'FileName'  => 'Varchar(255)',
	);

	static $indexes = array(
		'FileName' => array('type'=>'unique', 'value'=>'FileName'),
	);

	/**
	 * @return int
	 */
	public function getIdentifier()
	{
		return (int)$this->getField('ID');
	}

	/**
	 * @return string
	 */
	public function name()
	{
		return (string)$this->getField('FileName');
	}
}