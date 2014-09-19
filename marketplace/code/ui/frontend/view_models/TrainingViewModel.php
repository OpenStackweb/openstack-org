<?php

/**
 * Class TrainingViewModel
 */
class TrainingViewModel extends ViewableData {

	/**
	 * @var ArrayList
	 */
	private $courses;
	/**
	 * @var ICompany
	 */
	private $company;
	/**
	 * @var int
	 */
	private $id;
	/**
	 * @var string
	 */
	private $name;
	/**
	 * @var string
	 */
	private $description;

	/**
	 * @param int      $id
	 * @param string   $name
	 * @param string   $description
	 * @param ICompany $company
	 */
	public function __construct($id,$name,$description, ICompany $company){
		$this->id          = $id;
		$this->name        = $name;
		$this->description = $description;
		$this->company     = $company;
	}

	/**
	 * @return int
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getName(){
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function getDescription(){
		return $this->description;
	}

	/**
	 * @return ICompany
	 */
	public function getCompany(){
		return $this->company;
	}

	/**
	 * @param ArrayList $courses
	 */
	public function setCourses(ArrayList $courses){
		$this->courses = $courses;
	}

	/**
	 * @return ArrayList
	 */
	public function getCourses(){
		return $this->courses;
	}
}