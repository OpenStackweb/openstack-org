<?php

/**
 * Class PrivateCloud_Controllerextends
 */
final class PrivateCloud_Controller extends Cloud_Controller {

	function init()	{
		$this->cloud_repository   = new SapphirePrivateCloudRepository;
		$this->clouds_names_query = new PrivateCloudsNamesQueryHandler;
		parent::init();
	}

	/**
	 * @return string
	 */
	function getDirectoryPageClass()
	{
		return 'PrivateCloudsDirectoryPage';
	}

	/**
	 * @return CloudService
	 */
	function  getCloudTypeClass()
	{
		return new PrivateCloudService;
	}
}