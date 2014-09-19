<?php
/**
 * Class SpokenLanguage
 */
final class SpokenLanguage extends DataObject
implements ISpokenLanguage {

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
	 * @param string $name
	 * @return void
	 */
	public function setName($name)
	{
		$this->setField('Name',$name);
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->getField('Name');
	}

	/**
	 * @return int
	 */
	public function getOrder()
	{
		if($this->hasField('Order'))
			return (int)$this->getField('Order');
		return false;
	}
}