<?php

/**
 * Class GuestOSType
 */
class GuestOSType extends DataObject implements IGuestOSType {

	static $create_table_options = array('MySQLDatabase' => 'ENGINE=InnoDB');

	static $db = array(
		'Type' => 'Varchar',
	);

	static $summary_fields = array(
		'Type' => 'OS Type',
	);

	static $indexes = array(
		'Type' => array('type'=>'unique', 'value'=>'Type')
	);

	/**
	 * @return int
	 */
	public function getIdentifier()
	{
		return (int)$this->getField('ID');
	}

	public function getType()
	{
		return $this->getField('Type');
	}

	public function setType($type)
	{
		$this->setField('Type',$type);
	}
} 