<?php

/**
 * Class TeamFactory
 */
final class TeamFactory implements ITeamFactory {

	/**
	 * @param array $team_data
	 * @return ITeam
	 */
	public function buildTeam(array $team_data)
	{
		$team = new Team();
		$team->Name        = $team_data['name'];
		$team->CompanyID   = (int)$team_data['company_id'];
		$team->Company     = new Company() ;
		$team->Company->ID = (int)$team_data['company_id'];
		return $team;
	}
} 