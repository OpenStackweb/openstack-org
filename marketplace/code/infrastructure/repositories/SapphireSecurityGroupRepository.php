<?php

/**
 * Class SapphireSecurityGroupRepository
 */
class SapphireSecurityGroupRepository
	extends SapphireRepository
	implements ISecurityGroupRepository {

	public function __construct(){
		parent::__construct(new Group);
	}

	/**
	 * @param $title
	 * @return ISecurityGroup
	 */
	public function getByTitle($title)
	{
		return Group::get()->filter('Title',$title)->first();
	}
}