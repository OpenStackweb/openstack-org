<?php
/**
 * Interface ICompanyServiceRepository
 */
interface ICompanyServiceRepository extends IEntityRepository {
	/**
	 * @param int $company_id
	 * @return int
	 */
	public function countByCompany($company_id);

	/**
	 * @param string $list
	 * @return ICompanyService[]
	 */
	public function getActivesByList($list);

	/**
	 * @return ICompanyService[]
	 */
	public function getActivesRandom();

	/**
	 * @return int
	 */
	public function countActives();
}