<?php
/**
 * Class SapphireConfigurationManagementTypeRepository
 */
final class SapphireConfigurationManagementTypeRepository
	extends SapphireRepository
{
	public function __construct(){
		parent::__construct(new ConfigurationManagementType);
	}
} 