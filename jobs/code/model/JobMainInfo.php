<?php
/**
 * Class JobMainInfo
 */
final class JobMainInfo {

	/**
	 * @var string
	 */
	private $title;
	/**
	 * @var Company
	 */
	private $company;
	/**
	 * @var string
	 */
	private $url;
	/**
	 * @var string
	 */
	private $description;
	/**
	 * @var string
	 */
	private $instructions_2_apply;
	/**
	 * @var DateTime
	 */
	private $expiration_date;

	/**
	 * @var string
	 */
	private $location_type;
	/**
	 * @param string $title
	 * @param Company $company
	 * @param string $url
	 * @param string $description
	 * @param string $instructions_2_apply
	 * @param string $location_type
	 * @param DateTime $expiration_date
	 */
	public function __construct($title, $company, $url, $description, $instructions_2_apply, $location_type, $expiration_date = null){
		$this->title                = $title;
		$this->company              = $company;
		$this->url                  = $url;
		$this->description          = $description;
		$this->instructions_2_apply = $instructions_2_apply;
		$this->location_type        = $location_type;
		$this->expiration_date      = $expiration_date;

	}

	/**
	 * @return string
	 */
	public function getTitle(){
		return $this->title;
	}

	/**
	 * @return Company
	 */
	public function getCompany(){
		return $this->company;
	}

	/**
	 * @return string
	 */
	public function getUrl(){
		return $this->url;
	}

	/**
	 * @return string
	 */
	public function getDescription(){
		return $this->description;
	}

	/**
	 * @return string
	 */
	public function getInstructions(){
		return $this->instructions_2_apply;
	}

	/**
	 * @return string
	 */
	public function getLocationType(){
		return $this->location_type;
	}

	/**
	 * @return DateTime|null
	 */
	public function getExpirationDate(){
		return $this->expiration_date;
	}
} 