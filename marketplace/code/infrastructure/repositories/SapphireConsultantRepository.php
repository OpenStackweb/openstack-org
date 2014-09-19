<?php
/**
 * Class SapphireConsultantRepository
 */
final class SapphireConsultantRepository
	extends SapphireRegionalSupportedCompanyServiceRepository {
	public function __construct(){
		parent::__construct(new Consultant);
	}

	public function delete(IEntity $entity){
		$entity->clearClients();
		$entity->clearOffices();
		$entity->clearSpokenLanguages();
		$entity->clearExpertiseAreas();
		$entity->clearServicesOffered();
		parent::delete($entity);
	}

	/**
	 * @return string
	 */
	protected function getMarketPlaceTypeGroup()
	{
		return IConsultant::MarketPlaceGroupSlug;
	}
}