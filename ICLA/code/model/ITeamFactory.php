<?php

/**
 * Interface ITeamFactory
 */
interface ITeamFactory {
	/**
	 * @param array $team_data
	 * @return ITeam
	 */
	public function buildTeam(array $team_data);
} 