<?php
/**
 * Class ConfigurationManagementType
 */
class ConfigurationManagementType
	extends DataObject
	implements IConfigurationManagementType
{

	static $create_table_options = array('MySQLDatabase' => 'ENGINE=InnoDB');

	static $db = array(
		'Type'  => 'Varchar',
	);

	static $indexes = array(
		'Type' => array('type'=>'unique', 'value'=>'Type')
	);

	static $summary_fields = array(
		'Type' => 'Type',
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
}