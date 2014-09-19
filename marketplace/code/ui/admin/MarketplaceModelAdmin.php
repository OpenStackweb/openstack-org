<?php

/**
 * Class MarketplaceModelAdmin
 */
class MarketplaceModelAdmin extends ModelAdmin {

	public static $managed_models = array(
		'TrainingService',
		'TrainingCourseType',
		'TrainingCourseLevel',
		'TrainingCoursePrerequisite',
		'GuestOSType',
		'HyperVisorType',
		'PricingSchemaType',
		'SpokenLanguage',
		'Region',
		'ConfigurationManagementType',
		'SupportChannelType',
		'MarketPlaceVideoType',
		'OpenStackComponent',
		'OpenStackRelease',
	);

	public $showImportForm = false;
	static $url_segment = 'marketplace';
	static $menu_title  = 'Marketplace';

	public function init()
	{
		parent::init();
	}

}