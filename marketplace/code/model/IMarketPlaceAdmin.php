<?php
/**
 * Interface IMarketPlaceAdmin
 */
interface IMarketPlaceAdmin {
	/**
	 * @param string $type
	 * @param int $company_id
	 * @return bool
	 */
	public function isMarketPlaceAdminOfCompany($type, $company_id);

	/**
	 * @return bool
	 */
	public function isMarketPlaceAdmin();

	/**
	 * @return bool
	 */
	public function isMarketPlaceSuperAdmin();

	/**
	 * @param string $type
	 * @return ICompany[]
	 */
	public function getManagedMarketPlaceCompaniesByType($type);

	/**
	 * @param string $type
	 * @return bool
	 */
	public function isMarketPlaceTypeAdmin($type);

	/**
	 * @param string $type
	 * @return bool
	 */
	public function isMarketPlaceTypeSuperAdmin($type);
} 