<?php

/**
 * Class ConsultantServiceOfferedType
 */
class ConsultantServiceOfferedType
	extends DataObject
	implements IConsultantServiceOfferedType
{

	static $create_table_options = array('MySQLDatabase' => 'ENGINE=InnoDB');

	static $db = array(
		'Type' => 'Text',
	);
	/**
	 * @return string
	 */
	public function getType()
	{
		return $this->getField('Type');
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
	 * @return int
	 */
	public function getIdentifier()
	{
		return (int)$this->getField('ID');
	}
	/**
	 * @return int
	 */
	public function getRegionId()
	{
		if($this->hasField('RegionID')){
			return (int)$this->getField('RegionID');
		}
		return false;
	}
}