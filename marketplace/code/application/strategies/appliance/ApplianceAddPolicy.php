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
 * Class ApplianceAddPolicy
 */
final class ApplianceAddPolicy implements IMarketPlaceTypeAddPolicy {

	/**
	 * @var IEntityRepository
	 */
	private $repository;

	/**
	 * @var IMarketplaceTypeRepository
	 */
	private $marketplace_type_repository;

	public function __construct(IEntityRepository $repository, IMarketplaceTypeRepository $marketplace_type_repository){
		$this->repository                  = $repository;
		$this->marketplace_type_repository = $marketplace_type_repository;
	}

	/**
	 * @param Company $company
	 * @return bool
	 * @throws PolicyException
	 */
	public function canAdd(Company $company)
	{
		$current = $this->repository->countByCompany($company->getIdentifier());
		$allowed = $company->getAllowedMarketplaceTypeInstances($this->marketplace_type_repository->getByType(IAppliance::MarketPlaceType));
		if($current >= $allowed)
			throw new PolicyException('ApplianceAddPolicy',sprintf('You reach the max. amount of %s (%s) per Company',"Appliances",$allowed));
		return true;
	}
}