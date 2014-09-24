<?php
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
	 * @param ICompany $company
	 * @return bool
	 * @throws PolicyException
	 */
	public function canAdd(ICompany $company)
	{
		$current = $this->repository->countByCompany($company->getIdentifier());
		$allowed = $company->getAllowedMarketplaceTypeInstances($this->marketplace_type_repository->getByType(IAppliance::MarketPlaceType));
		if($current >= $allowed)
			throw new PolicyException('ApplianceAddPolicy',sprintf('You reach the max. amount of %s (%s) per Company',"Appliances",$allowed));
		return true;
	}
}