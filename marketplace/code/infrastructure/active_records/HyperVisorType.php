<?php

/**
 * Class HyperVisorType
 */
class HyperVisorType extends DataObject implements IHyperVisorType{

	static $create_table_options = array('MySQLDatabase' => 'ENGINE=InnoDB');

	static $db = array(
		'Type' => 'Varchar',
	);

	static $indexes = array(
		'Type' => array('type'=>'unique', 'value'=>'Type')
	);

	static $summary_fields = array(
		'Type' => 'Type',
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