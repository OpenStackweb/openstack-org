<?php

/**
 * Interface ICompanyServiceFactory
 */
interface ICompanyServiceFactory {
	/**
	 * @param string           $name
	 * @param string           $overview
	 * @param ICompany         $company
	 * @param bool             $active
	 * @param IMarketPlaceType $marketplace_type
	 * @param null|string      $call_2_action_url
	 * @return ICompanyService
	 */
	public function buildCompanyService($name, $overview, ICompany $company, $active, IMarketPlaceType $marketplace_type,$call_2_action_url=null);

	/**
	 * @param $id
	 * @return ICompanyService
	 */
	public function buildCompanyServiceById($id);

} 