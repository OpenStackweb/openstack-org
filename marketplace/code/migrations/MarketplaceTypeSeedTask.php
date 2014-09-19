<?php

/**
 * Class MarketplaceTypeSeedTask
 * Creates all initial marketplace types
 */
class MarketplaceTypeSeedTask extends MigrationTask {

	protected $title = "Marketplace Type Seeding  Migration";

	protected $description = "Add all new Marketplaces Types to DB and its Security Groups";

	function up(){
		echo "Starting Migration Proc ...<BR>";
		//check if migration already had ran ...
		$migration = Migration::get()->filter('Name',$this->title)->first();

		if (!$migration) {

			$marketplace_types = array(
				'Training',
				'Public Cloud',
				'Appliance',
				'Distribution',
				'Consultant'
			);

			$factory = new MarketplaceFactory;
			$service = new MarketplaceTypeManager(
				new SapphireMarketPlaceTypeRepository,
				new SapphireSecurityGroupRepository,
				SapphireTransactionManager::getInstance());

			foreach($marketplace_types as $marketplace_type){
				try{
					$service->store($factory->buildMarketplaceType($marketplace_type));
				}
				catch(Exception $ex){

				}
			}
			DB::query("ALTER TABLE `OpenStackRelease_OpenStackComponents`
			ADD UNIQUE INDEX `Release_Component` (`OpenStackReleaseID` ASC, `OpenStackComponentID` ASC);");

			DB::query("ALTER TABLE `CloudServiceOffered_PricingSchemas`
			ADD UNIQUE INDEX `CloudServiceOffered_PricingSchemaType` (`CloudServiceOfferedID` ASC, `PricingSchemaTypeID` ASC);");

			$migration = new Migration();
			$migration->Name = $this->title;
			$migration->Description = $this->description;
			$migration->Write();
		}
		echo "Ending  Migration Proc ...<BR>";
	}

	function down()	{

	}
} 