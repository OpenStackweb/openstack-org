<?php
/**
 * Class CompaniesWithMarketplaceCreationRightsSapphireQueryHandler
 */
final class CompaniesWithMarketplaceCreationRightsSapphireQueryHandler
	implements ICompaniesWithMarketPlaceCreationRightsQueryHandler {

	/**
	 * @param IQuerySpecification $specification
	 * @return IQueryResult
	 */
	public function handle(IQuerySpecification $specification)
	{
		$params           = $specification->getSpecificationParams();
		$marketplace_type = $params[0];
		$companies        = array();
		$current_user     = Member::currentUser();
		if($current_user->isMarketPlaceSuperAdmin() || $current_user->isMarketPlaceTypeSuperAdmin($marketplace_type)){
		/**
		 * @todo:
		 * company has a distribution admin manager
		 * company has signed sponsorship contract
		 * company has signed all contracts related to distribution
		 * all contracts are valid
		 */
		$sql = <<< SQL
		SELECT C.ID,C.Name FROM Company C ORDER BY Name ASC;
SQL;
			$results = DB::query($sql);
			for ($i = 0; $i < $results->numRecords(); $i++) {
				$record = $results->nextRecord();
				array_push($companies,new CompanyDTO(
					(int)$record['ID'],
					$record['Name']
				));
			}
		}
		else{
			$res = array();
			switch(strtolower(trim($marketplace_type))){
				case 'implementation':{
					$res  = $current_user->getManagedMarketPlaceCompaniesByType(IAppliance::MarketPlaceGroupSlug);
					$res2 = $current_user->getManagedMarketPlaceCompaniesByType(IDistribution::MarketPlaceGroupSlug);
					if(count($res)>0 && count($res2)>0)
						$res = array_merge($res,array_diff_assoc($res ,$res2));
					else if(count($res) == 0)
						$res = $res2;
				}
				break;
				case 'distribution':
					$res = $current_user->getManagedMarketPlaceCompaniesByType(IDistribution::MarketPlaceGroupSlug);
				break;
				case 'appliance':
					$res = $current_user->getManagedMarketPlaceCompaniesByType(IAppliance::MarketPlaceGroupSlug);
				break;
				case 'public cloud':
					$res = $current_user->getManagedMarketPlaceCompaniesByType(IPublicCloudService::MarketPlaceGroupSlug);
				break;
				case 'private cloud':
					$res = $current_user->getManagedMarketPlaceCompaniesByType(IPrivateCloudService::MarketPlaceGroupSlug);
					break;
				case 'consultant':
					$res = $current_user->getManagedMarketPlaceCompaniesByType(IConsultant::MarketPlaceGroupSlug);
				break;
			}
			foreach(array_values($res) as $company){
				array_push($companies,new CompanyDTO(
					(int)$company->ID,
					$company->Name
				));
			}
		}
		return new CompaniesWithDistributionCreationRightsResult($companies);
	}
}