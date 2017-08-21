<?php
/**
 * Copyright 2014 Openstack Foundation
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 **/
/**
 * Class NewMarketplaceTypeBookSeedTask
 */
final class NewMarketplaceTypeBookSeedTask extends AbstractDBMigrationTask {

	protected $title = "New Marketplace Type \"Book\" Seeding  Migration";

	protected $description = "Add new Marketplace Type 'Book' to DB and its Security Group";

	function doUp(){
		$marketplace_types = array(
            'Book',
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

	}

	function doDown()	{

	}
}