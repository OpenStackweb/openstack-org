<?php

/**
 * Class PublicCloudSapphireRender
 */
final class PublicCloudSapphireRender {

	/**
	 * @var IPublicCloudService
	 */
	private $cloud;

	public function __construct(IPublicCloudService $cloud){
		$this->cloud = $cloud;
	}

	public function draw(){
		Requirements::css("marketplace/code/ui/frontend/css/marketplace.css");

        Requirements::combine_files('marketplace_public_cloud_instance.js', array(
            "marketplace/code/ui/frontend/js/openstack.implementation.capabilities.js",
            "marketplace/code/ui/frontend/js/marketplace.common.js",
            "marketplace/code/ui/frontend/js/cloud.page.js"
        ));

		return Controller::curr()->Customise($this->cloud)->renderWith(array('CloudsDirectoryPage_cloud', 'PublicCloudsDirectoryPage', 'MarketPlacePage'));
	}

    public function pdf(){
        return Controller::curr()->Customise($this->cloud)->renderWith(array('CloudsDirectoryPage_pdf'));
    }
} 