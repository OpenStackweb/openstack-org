<?php
/**
 * Class OpenStackApiVersion
 */
class OpenStackApiVersion extends DataObject implements IOpenStackApiVersion {

	static $create_table_options = array('MySQLDatabase' => 'ENGINE=InnoDB');

	static $db = array(
		'Version' => 'Varchar',
		'Status'  => "Enum('Deprecated, Current, Beta, Alpha' , 'Deprecated')",
	);

	static $summary_fields = array(
		'Version' => 'Version',
		'Status'  => 'Status',
	);

	static $indexes = array(
		'Version_Component' => array('type'=>'unique', 'value'=>'Version,OpenStackComponentID'),
	);

	static $has_one = array(
		'OpenStackComponent' => 'OpenStackComponent',
	);

	protected function onBeforeWrite() {
		parent::onBeforeWrite();
		$this->OpenStackComponentID = $this->getReleaseComponent()->getIdentifier();
	}

	/**
	 * @return int
	 */
	public function getIdentifier()
	{
		return (int)$this->getField('ID');
	}

	/**
	 * @param string $version
	 * @return void
	 */
	public function setVersion($version)
	{
		$this->setField('Version',$version);
	}

	/**
	 * @return string
	 */
	public function getVersion()
	{
		return $this->getField('Version');
	}

	/**
	 * @return IOpenStackComponent
	 */
	public function getReleaseComponent()
	{
		return AssociationFactory::getInstance()->getMany2OneAssociation($this,'OpenStackComponent','Versions')->getTarget();
	}

	/**
	 * @param IOpenStackComponent $new_component
	 * @return void
	 */
	public function setReleaseComponent(IOpenStackComponent $new_component)
	{
		AssociationFactory::getInstance()->getMany2OneAssociation($this,'OpenStackComponent','Versions')->setTarget($new_component);
	}

	/**
	 * @return string
	 */
	public function getStatus()
	{
		return $this->getField('Status');
	}

	/**
	 * @param string $status
	 * @return void
	 */
	public function setStatus($status)
	{
		$this->setField('Status',$status);
	}
}