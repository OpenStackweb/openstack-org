<?php

/**
 * Class Region
 */
class Region  extends DataObject implements IRegion {

	static $create_table_options = array('MySQLDatabase' => 'ENGINE=InnoDB');

	static $db = array(
		'Name'  => 'Varchar',
	);


	static $indexes = array(
		'Name' => array('type'=>'unique', 'value'=>'Name')
	);

	static $summary_fields = array(
		'Name' => 'Name',
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
	public function getName()
	{
		return $this->getField('Name');
	}

	/**
	 * @param string $name
	 * @return void
	 */
	public function setName($name)
	{
		$this->setField('Name',$name);
	}

}