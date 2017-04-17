<?php

/**
 * Class RemoteCloudDraftFactory
 */
final class RemoteCloudDraftFactory extends OpenStackImplementationDraftFactory {

	/**
	 * @param string           $name
	 * @param string           $overview
	 * @param ICompany         $company
	 * @param bool             $active
	 * @param IMarketPlaceType $marketplace_type
	 * @param null|string      $call_2_action_url
	 * @return ICompanyService
	 */
	public function buildCompanyService($name, $overview, ICompany $company, $active, IMarketPlaceType $marketplace_type, $call_2_action_url = null, $live_id = null, $published = null)
	{
		$remote_cloud = new RemoteCloudServiceDraft;
        $remote_cloud->setName($name);
        $remote_cloud->setOverview($overview);
        $remote_cloud->setCompany($company);
		if($active)
            $remote_cloud->activate();
		else
            $remote_cloud->deactivate();
        $remote_cloud->setMarketplace($marketplace_type);
        $remote_cloud->setCall2ActionUri($call_2_action_url);
        $remote_cloud->setLiveServiceId($live_id);
        $remote_cloud->setPublished($published);
		return $remote_cloud;
	}

    /**
     * @param $id
     * @return ICompanyService
     */
    public function buildCompanyServiceById($id)
    {
        $remote_cloud     = new RemoteCloudServiceDraft;
        $remote_cloud->ID = $id;
        return $remote_cloud;
    }

    /**
     * @param IRemoteCloudService $remote_cloud
     * @param                          $data
     */
    public function buildExtras(IRemoteCloudService $remote_cloud, $data)
    {
        $remote_cloud->setHardwareSpecifications($data['hardware_specifications']);
        $remote_cloud->setVendorManagedUpgrades($data['vendor_managed_upgrades']);
        $remote_cloud->setPricingModels($data['pricing_models']);
        $remote_cloud->setPublishedSLAs($data['published_slas']);

    }

}