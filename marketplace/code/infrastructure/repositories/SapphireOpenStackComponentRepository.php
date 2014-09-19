<?php

class SapphireOpenStackComponentRepository
	extends SapphireRepository
	implements IOpenStackComponentRepository {

	public function __construct(){
		parent::__construct(new OpenStackComponent);
	}
	/**
	 * @param string $name
	 * @return IOpenStackComponent
	 */
	public function getByName($name)
	{
		$class = $this->entity_class;
		return $class::get()->filter('Name',$name)->first();
	}

}