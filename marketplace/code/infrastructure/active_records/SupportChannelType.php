<?php

/**
 * Class SupportChannelType
 */
class SupportChannelType extends DataObject implements ISupportChannelType {

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

	static $has_one = array(
		'Icon' => 'BetterImage',
	);

	/**
	 * @return int
	 */
	public function getIdentifier()
	{
		return (int)$this->getField('ID');
	}

	/**
	 * @param string $type
	 * @return void
	 */
	public function setType($type)
	{
		$this->setField('Type',$type);
	}

	/**
	 * @return string
	 */
	public function getType()
	{
		return $this->getField('Type');
	}

	public function getInfo()
	{
		return $this->getField('Data');
	}
}