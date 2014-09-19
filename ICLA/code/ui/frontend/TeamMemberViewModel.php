<?php

/**
 * Class TeamMemberViewModel
 */
final class TeamMemberViewModel extends ViewableData {

	/**
	 * @var string
	 */
	private $first_name;
	/**
	 * @var string
	 */
	private $last_name;
	/**
	 * @var string
	 */
	private $email;
	/**
	 * @var string
	 */
	private $team_name;
	/**
	 * @var string
	 */
	private $status;
	/**
	 * @var int
	 */
	private $id;

	/**
	 * @var int
	 */
	private $team_id;

	private $date_added;

	/**
	 * @param string $first_name
	 * @param string $last_name
	 * @param string $email
	 * @param string $team_name
	 * @param int    $team_id
	 * @param string $status
	 * @param int $id
	 * @param $date_added
	 */
	public function __construct($first_name, $last_name, $email, $team_id, $team_name, $status, $id, $date_added){
		$this->first_name = $first_name;
		$this->last_name  = $last_name;
		$this->email      = $email;
		$this->team_name  = $team_name;
		$this->team_id    = $team_id;
		$this->status     = $status;
		$this->id         = $id;
		$this->date_added = $date_added;
	}

	/**
	 * @return string
	 */
	public function getStatus(){
		return $this->status;
	}

	public function getFirstName(){
		return $this->first_name;
	}

	public function getLastName(){
		return $this->last_name;
	}

	public function getEmail(){
		return $this->email;
	}

	public function getId(){
		return $this->id;
	}

	public function getTeamName(){
		return $this->team_name;
	}

	public function getTeamId(){
		return $this->team_id;
	}

	public function getDateAdded(){
		return $this->date_added;
	}

} 