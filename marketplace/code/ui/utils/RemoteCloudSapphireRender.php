<?php

/**
 * Class RemoteCloudSapphireRender
 */
final class RemoteCloudSapphireRender {

	/**
	 * @var IRemoteCloudService
	 */
	private $remote_cloud;

	public function __construct(IRemoteCloudService $remote_cloud){
		$this->remote_cloud = $remote_cloud;
	}

	public function draw(){
		Requirements::javascript("marketplace/code/ui/frontend/js/openstack.implementation.capabilities.js");
        Requirements::javascript("marketplace/code/ui/frontend/js/marketplace.common.js");
		Requirements::javascript("marketplace/code/ui/frontend/js/implementation.page.js");
		Requirements::css("marketplace/code/ui/frontend/css/marketplace.css");
		return Controller::curr()->Customise($this->remote_cloud )->renderWith(array('RemoteCloudsDirectoryPage_implementation', 'RemoteCloudsDirectoryPage', 'MarketPlacePage'));
	}

    public function pdf(){
        return Controller::curr()->Customise($this->remote_cloud )->renderWith(array('RemoteCloudsDirectoryPage_pdf'));
    }
} 