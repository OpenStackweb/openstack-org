<?php

/**
 * Class PrivateCloudFactory
 */
final class PrivateCloudFactory extends CloudFactory {
	/**
	 * @param string           $name
	 * @param string           $overview
	 * @param ICompany         $company
	 * @param bool             $active
	 * @param IMarketPlaceType $marketplace_type
	 * @param null|string      $call_2_action_url
	 * @return ICompanyService
	 */
	public function buildCompanyService($name, $overview, ICompany $company, $active, IMarketPlaceType $marketplace_type, $call_2_action_url = null)
	{
		$private_cloud = new PrivateCloudService;
		$private_cloud->setName($name);
		$private_cloud->setOverview($overview);
		$private_cloud->setCompany($company);
		if($active)
			$private_cloud->activate();
		else
			$private_cloud->deactivate();
		$private_cloud->setMarketplace($marketplace_type);
		$private_cloud->setCall2ActionUri($call_2_action_url);
		return $private_cloud;
	}

	/**
	 * @param $id
	 * @return ICompanyService
	 */
	public function buildCompanyServiceById($id)
	{
		$private_cloud     = new PrivateCloudService;
		$private_cloud->ID = $id;
		return $private_cloud;
	}
}