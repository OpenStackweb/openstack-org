<?php
/**
 * Class SapphireRegionalSupportedCompanyServiceRepository
 */
abstract class SapphireRegionalSupportedCompanyServiceRepository
extends SapphireCompanyServiceRepository
{
	/**
	 * @param IEntity $entity
	 */
	public function __construct(IEntity $entity){
		parent::__construct($entity);
	}

	/**
	 * @param IEntity $entity
	 * @return void
	 */
	public function delete(IEntity $entity){
		$entity->clearRegionalSupports();
		parent::delete($entity);
	}

	/**
	 * @return string
	 */
	protected abstract function getMarketPlaceTypeGroup();

	/**
	 * @param QueryObject $query
	 * @param int         $offset
	 * @param int         $limit
	 * @return array
	 */
	public function getAll(QueryObject $query, $offset = 0, $limit = 10){
		$filter         = (string) $query;
		$current_member =  Member::currentUser();
		$do             = null;
		if($current_member && !$current_member->isMarketPlaceSuperAdmin()){
			//if current user is super admin get all
			//if not , get just related companies
			$companies = $current_member->getManagedMarketPlaceCompaniesByType($this->getMarketPlaceTypeGroup());
			if(count($companies)){
				$company_filter = ' CompanyID IN ( ';
				foreach($companies as $company){
					$company_filter .= $company->getIdentifier().',';
				}
				$company_filter =   trim($company_filter,',');
				$company_filter .= ' ) ';
				if(!empty($filter)){
					$company_filter = ' AND '.$company_filter;
				}
				$filter = $filter.$company_filter;
			}
		}

		$joins   = $query->getAlias();

		//build query for data object
		$class = $this->entity_class;
		$do = $class::get()->where($filter)->sort($query->getOrder())->limit($limit, $offset);
		foreach($joins as $table => $on){
			$do = $do->innerJoin($table,$on);
		}

		if(is_null($do)) return array(array(),0);
		$res    = $do->toArray();
		foreach ($res as $entity) {
			UnitOfWork::getInstance()->scheduleForUpdate($entity);
		}
		return array($res, (int) $do->count());
	}
}