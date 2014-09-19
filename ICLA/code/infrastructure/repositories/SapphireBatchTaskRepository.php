<?php
final class SapphireBatchTaskRepository
	extends SapphireRepository
	implements  IBatchTaskRepository{

	public function __construct(){
		parent::__construct(new BatchTask());
	}

	/***
	 * @param string $name
	 * @return IBatchTask
	 */
	public function findByName($name)
	{
		$query = new QueryObject;
		$query->addAddCondition(QueryCriteria::equal('Name',$name));
		return $this->getBy($query);
	}
}