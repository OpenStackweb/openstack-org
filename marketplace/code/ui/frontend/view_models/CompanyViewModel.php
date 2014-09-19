<?php

/**
 * Class CompanyViewModel
 */
class CompanyViewModel extends ViewableData {
	private $dto;

	public function __construct(CompanyDTO $dto){
		$this->dto = $dto;
	}

	public function getID(){
		return $this->dto->getID();
	}

	public function getName(){
		return $this->dto->getName();
	}
} 