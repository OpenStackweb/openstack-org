<?php

/**
 * Class SapphireICLACompanyRepository
 */
final class SapphireICLACompanyRepository
	extends SapphireRepository
	implements ICLACompanyRepository {

	public function __construct(){
		$entity = new ICLACompanyDecorator;
		$entity->setOwner(new Company);
		parent::__construct($entity);
	}

} 