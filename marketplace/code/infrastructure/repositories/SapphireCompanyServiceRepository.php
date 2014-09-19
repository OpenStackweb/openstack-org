<?php
/**
 * Class SapphireCompanyServiceRepository
 */
abstract class SapphireCompanyServiceRepository
	extends SapphireRepository
	implements ICompanyServiceRepository {

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
	public function delete(IEntity $entity)
	{
		$entity->clearVideos();
		$entity->clearResources();
		parent::delete($entity);
	}

	/**
	 * @param int $company_id
	 * @return int
	 */
	public function countByCompany($company_id){
		$count = DB::query("SELECT COUNT(*) FROM CompanyService WHERE ClassName='{$this->entity_class}' AND CompanyID = {$company_id} ")->value();
		return intval($count);
	}

	/**
	 * @return int
	 */
	public function countActives()
	{
		return (int)DB::query("SELECT COUNT(*) FROM CompanyService WHERE ClassName = '{$this->entity_class}' AND Active = 1 ; ")->value();
	}

	/**
	 * @return ICompanyService[]
	 */
	public function getActivesRandom()
	{
		$class = $this->entity_class;
		$ds =  $class::get()->filter('Active',1)->sort('RAND()');
		return is_null($ds)?array():$ds->toArray();
	}

	/**
	 * @param string $list
	 * @return ICompanyService[]
	 */
	public function getActivesByList($list)	{
		$order = "'".implode( "' , '", explode(', ',$list)). "'";
		$class = $this->entity_class;
		$ds           = $class::get()->filter('Active',1)->where("ID IN ({$list})")->sort("FIELD(ID, {$order})");
		$res          = is_null($ds)?array():$ds->toArray();
		return $res;
	}
}