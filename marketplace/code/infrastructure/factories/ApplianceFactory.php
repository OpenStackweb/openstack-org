<?php

/**
 * Class ApplianceFactory
 */
final class ApplianceFactory extends OpenStackImplementationFactory {

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
		$appliance = new Appliance;
		$appliance->setName($name);
		$appliance->setOverview($overview);
		$appliance->setCompany($company);
		if($active)
			$appliance->activate();
		else
			$appliance->deactivate();
		$appliance->setMarketplace($marketplace_type);
		$appliance->setCall2ActionUri($call_2_action_url);
		return $appliance;
	}

	/**
	 * @param $id
	 * @return ICompanyService
	 */
	public function buildCompanyServiceById($id)
	{
		$appliance     = new Appliance;
		$appliance->ID = $id;
		return $appliance;
	}

}