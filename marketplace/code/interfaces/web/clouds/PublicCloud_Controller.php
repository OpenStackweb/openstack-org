<?php

/**
 * Class PublicCloud_Controller
 */
final class PublicCloud_Controller extends Cloud_Controller {

	function init()	{
		$this->cloud_repository   = new SapphirePublicCloudRepository;
		$this->clouds_names_query = new PublicCloudsNamesQueryHandler;
		parent::init();
	}

	/**
	 * @return string
	 */
	function getDirectoryPageClass()
	{
		return 'PublicCloudsDirectoryPage';
	}

	/**
	 * @return CloudService
	 */
	function  getCloudTypeClass()
	{
		return new PublicCloudService;
	}
}