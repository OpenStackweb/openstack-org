<?php

/**
 * Class ApplianceDraftFactory
 */
final class ApplianceDraftFactory extends OpenStackImplementationDraftFactory {

	/**
	 * @param string           $name
	 * @param string           $overview
	 * @param Company          $company
	 * @param bool             $active
	 * @param IMarketPlaceType $marketplace_type
	 * @param null|string      $call_2_action_url
	 * @return ICompanyService
	 */
	public function buildCompanyService($name, $overview, Company $company, $active, IMarketPlaceType $marketplace_type, $call_2_action_url = null, $live_id = null, $published = null)
	{
		$appliance = new ApplianceDraft;
		$appliance->setName($name);
		$appliance->setOverview($overview);
		$appliance->setCompany($company);
		if($active)
			$appliance->activate();
		else
			$appliance->deactivate();
		$appliance->setMarketplace($marketplace_type);
		$appliance->setCall2ActionUri($call_2_action_url);
        $appliance->setLiveServiceId($live_id);
        $appliance->setPublished($published);
		return $appliance;
	}

	/**
	 * @param $id
	 * @return ICompanyService
	 */
	public function buildCompanyServiceById($id)
	{
		$appliance     = new ApplianceDraft;
		$appliance->ID = $id;
		return $appliance;
	}

}