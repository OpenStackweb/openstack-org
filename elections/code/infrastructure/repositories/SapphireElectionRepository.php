<?php

/**
 * Class SapphireElectionRepository
 */
final class SapphireElectionRepository extends SapphireRepository
	implements IElectionRepository  {
	public function __construct(){
		parent::__construct(new Election());
	}

	/**
	 * @param int $n
	 * @return IElection[]
	 */
	public function getLatestNElections($n)
	{
		$query = new QueryObject(new Election);
		$query->addOrder(QueryOrder::desc('ElectionsOpen'));
		list($list,$count) = $this->getAll($query,0,$n);
		return $list;
	}

	/**
	 * @param int $years
	 * @return IElection
	 */
	public function getEarliestElectionSince($years)
	{
		$sql = 'select * from Election where ElectionsClose >= date_add(now(), interval -'.$years.' year) ORDER BY ElectionsClose ASC LIMIT 0,1;';
		$res = DB::query($sql);
		// let Silverstripe work the magic
		$elections = singleton('Election')->buildDataObjectSet($res);
		return $elections->first();
	}
}