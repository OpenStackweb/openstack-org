<?php

/**
 * Class MarketPlaceVideoType
 */
class MarketPlaceVideoType extends DataObject implements IMarketPlaceVideoType {

	static $create_table_options = array('MySQLDatabase' => 'ENGINE=InnoDB');

	static $db = array(
		'Type'               => 'Varchar',
		'Title'              => 'Text',
		'Description'        => 'Text',
		'MaxTotalVideoTime'  => 'int',
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

	public function getMaxTotalTime()
	{
		return (int)$this->getField('MaxTotalVideoTime');
	}

	public function setMaxTotalTime($time)
	{
		$this->setField('MaxTotalVideoTime',$time);
	}
}