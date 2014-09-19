<?php
/**
 * Class SapphireConsultantServiceOfferedTypeRepository
 */
final class SapphireConsultantServiceOfferedTypeRepository
	extends SapphireRepository {

	public function __construct(){
		parent::__construct(new ConsultantServiceOfferedType);
	}
} 