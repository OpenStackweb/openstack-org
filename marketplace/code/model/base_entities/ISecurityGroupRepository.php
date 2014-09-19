<?php

/**
 * Interface IGroupRepository
 */
interface ISecurityGroupRepository extends IEntityRepository {
	/**
	 * @param $title
	 * @return ISecurityGroup
	 */
	public function getByTitle($title);

} 