<?php
/***
 * Class SapphireTeamRepository
 */
final class SapphireTeamRepository extends SapphireRepository
implements ITeamRepository
{
	public function __construct(){
		parent::__construct(new Team);
	}

	/**
	 * @param int $company_id
	 * @return ITeam[]
	 */
	public function getByCompany($company_id) {
		$query = new QueryObject(new Team);
		$query->addAddCondition(QueryCriteria::equal('CompanyID', $company_id));
		list($list, $size) = $this->getAll($query, 0, 1000);
		return $list;
	}

	/**
	 * @param string $name
	 * @param int    $company_id
	 * @return ITeam
	 */
	public function getByNameAndCompany($name, $company_id)
	{
		$query = new QueryObject(new Team);

		$query->addAddCondition(QueryCriteria::equal('CompanyID', $company_id));
		$query->addAddCondition(QueryCriteria::equal('Name', $name));

		return $this->getBy($query);
	}
}