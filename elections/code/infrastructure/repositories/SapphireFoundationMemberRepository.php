<?php

/**
 * Class SapphireFoundationMemberRepository
 */
final class SapphireFoundationMemberRepository
	extends SapphireRepository
	implements IFoundationMemberRepository{


	public function __construct(){
		$entity = new FoundationMember();
		$entity->setOwner(new Member());
		parent::__construct($entity);
	}

	/**
	 * @param int                 $n
	 * @param int                 $limit
	 * @param int                 $offset
	 * @param IElectionRepository $election_repository
	 * @return int[]
	 */
	public function getMembersThatNotVotedOnLatestNElections($n, $limit, $offset, IElectionRepository $election_repository)
	{
		$specification = new FoundationMembershipRevocationSpecification;
		$sql = $specification->sql($n,$necessary_votes = 2 ,$election_repository,$offset,$limit);
		$res = DB::query($sql);
		$list = array();
		foreach ($res as $record) {
			array_push($list,(int)$record["ID"]);
		}
		return $list;
	}

	/**
	 * @param string $first_name
	 * @param string $last_name
	 * @return IFoundationMember
	 */
	public function getByCompleteName($first_name, $last_name)
	{
		$query = new QueryObject(new Member());
		$query->addAddCondition(QueryCriteria::equal('FirstName',$first_name));
		$query->addAddCondition(QueryCriteria::equal('Surname',$last_name));
		return $this->getBy($query);
	}
}