<?php

/**
 * Class MarketPlaceType
 */
class MarketPlaceType extends DataObject implements IMarketPlaceType  {

	static $create_table_options = array('MySQLDatabase' => 'ENGINE=InnoDB');

	static $db = array(
		'Name'           => 'Varchar',
		'Slug'           => 'Varchar',
		'Active'         => 'Boolean',
	);

	public static $summary_fields = array(
		'Name'
	);

	static $indexes = array(
		'Name' => array('type'=>'unique', 'value'=>'Name'),
		'Slug' => array('type'=>'unique', 'value'=>'Slug')
	);


	static $has_one = array(
		'AdminGroup' => 'Group',
	);

	static $has_many = array(
		'Services'          => 'CompanyService',
		'ContractTemplates' => 'ContractTemplate',
	);


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

	/**
	 * @return string
	 */
	public function getSlug()
	{
		return $this->getField('Slug');
	}

	/**
	 * @param string $slug
	 * @return void
	 */
	public function setSlug($slug)
	{
		$this->setField('Slug',$slug);
	}

	/**
	 * @return bool
	 */
	public function isActive()
	{
		return (bool)$this->getField('Active');
	}

	/**
	 * @return void
	 */
	public function activate()
	{
		$this->setField('Active',true);
	}

	/**
	 * @return void
	 */
	public function deactivate()
	{
		$this->setField('Active',false);
	}

	/**
	 * @return string
	 */
	public function getAdminGroupSlug()
	{
		$g = $this->getAdminGroup();
		if($g){
			return $g->Code;
		}
		return false;
	}


	public function setAdminGroup(ISecurityGroup $group){
		AssociationFactory::getInstance()->getMany2OneAssociation($this,'AdminGroup')->setTarget($group);
	}

	public function getAdminGroup(){
		return AssociationFactory::getInstance()->getMany2OneAssociation($this,'AdminGroup')->getTarget();
	}

	public function getIdentifier()
	{
		return (int)$this->getField('ID');
	}

	protected function onBeforeWrite() {
		parent::onBeforeWrite();
		$this->AdminGroupID = $this->getAdminGroup()->getIdentifier();
	}

	/**
	 * @return ISecurityGroup
	 */
	public function createSecurityGroup(){
		$name             = $this->getName();
		$group            = new Group;
		$group->setTitle("MarketPlace {$name} Administrators");
		return $group;
	}
}