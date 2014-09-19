<?php

/**
 * Class OpenStackReleaseSupportedApiVersion
 */
class OpenStackReleaseSupportedApiVersion
	extends DataObject
	implements IReleaseSupportedApiVersion
{

	static $create_table_options = array('MySQLDatabase' => 'ENGINE=InnoDB');

	static $has_one = array(
		'OpenStackComponent'  => 'OpenStackComponent',
		'ApiVersion'          => 'OpenStackApiVersion',
		'Release'             => 'OpenStackRelease',
	);

	static $indexes = array(
		'Component_ApiVersion_Release' => array('type'=>'unique', 'value'=>'OpenStackComponentID,ApiVersionID,ReleaseID')
	);

	/**
	 * @return int
	 */
	public function getIdentifier()
	{
		return (int)$this->getField('ID');
	}

	/**
	 * @param IOpenStackApiVersion $version
	 * @return void
	 */
	public function setApiVersion(IOpenStackApiVersion $version)
	{
		AssociationFactory::getInstance()->getMany2OneAssociation($this,'ApiVersion')->setTarget($version);
	}

	/**
	 * @return IOpenStackApiVersion
	 */
	public function getApiVersion()
	{
		return AssociationFactory::getInstance()->getMany2OneAssociation($this,'ApiVersion')->getTarget();
	}

	/**
	 * @param IOpenStackRelease $release
	 * @return void
	 */
	public function setRelease(IOpenStackRelease $release)
	{
		AssociationFactory::getInstance()->getMany2OneAssociation($this,'Release','SupportedApiVersions')->setTarget($release);
	}

	/**
	 * @return IOpenStackRelease
	 */
	public function getRelease()
	{
		return AssociationFactory::getInstance()->getMany2OneAssociation($this,'Release','SupportedApiVersions')->getTarget();
	}

	/**
	 * @param IOpenStackComponent $component
	 * @return void
	 */
	public function setOpenStackComponent(IOpenStackComponent $component)
	{
		AssociationFactory::getInstance()->getMany2OneAssociation($this,'OpenStackComponent')->setTarget($component);
	}

	/**
	 * @return IOpenStackComponent
	 */
	public function getOpenStackComponent()
	{
		return AssociationFactory::getInstance()->getMany2OneAssociation($this,'OpenStackComponent')->getTarget();
	}
}