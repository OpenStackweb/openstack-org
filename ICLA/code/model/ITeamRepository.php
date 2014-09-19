<?php

/**
 * Interface ITeamRepository
 */
interface  ITeamRepository extends IEntityRepository {
	/**
	 * @param int $company_id
	 * @return ITeam[]
	 */
	public function getByCompany($company_id);

	/**
	 * @param string $name
	 * @param int $company_id
	 * @return ITeam
	 */
	public function getByNameAndCompany($name, $company_id);
} 