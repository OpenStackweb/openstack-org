<?php
/**
 * Class MarketPlaceVideoTypeManager
 */
final class MarketPlaceVideoTypeManager extends AbstractEntityManager {


	/**
	 * @param IEntityRepository $repository
	 * @param ITransactionManager             $tx_manager
	 */

	public function __construct(IEntityRepository $repository,
	                            ITransactionManager $tx_manager){
		parent::__construct('MarketplaceVideoType',
			                array('type','max_total_time'),
			                $repository,
			                $tx_manager);

	}

	/**
	 * @param IMarketPlaceVideoType $entity
	 * @return int
	 */
	public function store(IMarketPlaceVideoType $entity){

		$repository = $this->repository;
		$res = false;
		$this->tx_manager->transaction(function() use(&$res, &$entity, $repository){

			$query = new QueryObject;
			$query->addAddCondition(QueryCriteria::equal('Type', $entity->getType()));
			$old_one = $repository->get($query);
			if($old_one)
				throw new EntityAlreadyExistsException('MarketplaceVideoType',sprintf('type  %s',$entity->getType()));
			$res = $repository->add($entity);
		});
		return $res;
	}

	/**
	 * @param int   int $id
	 * @param array $params
	 * @return void
	 * @throws EntityAlreadyExistsException
	 */
	protected function checkDuplicatedEntityCriteria($id, array $params)
	{
		if(@$params['type']){
			$query = new QueryObject;
			$query->addAddCondition(QueryCriteria::equal('Type',$params['type']));
			$query->addAddCondition(QueryCriteria::notEqual('ID',$id));
			$old_one = $this->repository->get($query);
			if($old_one)
				throw new EntityAlreadyExistsException($this->entity_class, sprintf('%s  %s','type',  $params['type']));
		}
	}

	/**
	 * @param IEntity $entity
	 * @param array   $params
	 * @return mixed|void
	 */
	protected function customUpdateLogic(IEntity $entity, array $params)
	{
		// TODO: Implement customUpdateLogic() method.
	}
}