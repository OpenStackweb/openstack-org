<?php

/**
 * Class SapphireCLAMemberRepository
 */
final class SapphireCLAMemberRepository
	extends SapphireRepository
	implements ICLAMemberRepository {

	public function __construct(){
		$entity = new ICLAMemberDecorator;
		$entity->setOwner(new Member);
		parent::__construct($entity);
	}

	/***
	 * @return int[]
	 */
	function getAllGerritIds() {
		$gerrit_ids = DB::query('SELECT GerritID FROM Member WHERE GerritID IS NOT NULL;');
		$res = array();
		foreach ($gerrit_ids as $id) {
			$gerrit_id       = (string) $id['GerritID'];
			$res[$gerrit_id] = $gerrit_id;
		}
		return $res;
	}

	/**
	 * @param int $offset
	 * @param int $limit
	 * @return ICLAMember[]
	 */
	function getAllICLAMembers($offset, $limit)
	{
		$query = new QueryObject;
		$query->addAddCondition(QueryCriteria::equal('CLASigned',true));
		return $this->getAll($query, $offset, $limit);
	}

	/**
	 * @param string $email
	 * @return ICLAMember
	 */
	public function findByEmail($email){
		$query = new QueryObject;
		$query->addAddCondition(QueryCriteria::equal('Email',$email));
		return $this->getBy($query);
	}

	/**
	 * @param string $email
	 * @param int    $offset
	 * @param int    $limit
	 * @return array
	 */
	function getAllIClaMembersByEmail($email, $offset, $limit)
	{
		$query = new QueryObject;
		$query->addAddCondition(QueryCriteria::equal('CLASigned',true));
		$query->addAddCondition(QueryCriteria::like('Email',$email));
		return $this->getAll($query, $offset, $limit);
	}

	/**
	 * @param string $first_name
	 * @param int    $offset
	 * @param int    $limit
	 * @return array
	 */
	function getAllIClaMembersByFirstName($first_name, $offset, $limit)
	{
		$query = new QueryObject;
		$query->addAddCondition(QueryCriteria::equal('CLASigned',true));
		$query->addAddCondition(QueryCriteria::like('FirstName',$first_name));
		return $this->getAll($query, $offset, $limit);
	}

	/**
	 * @param string $last_name
	 * @param int    $offset
	 * @param int    $limit
	 * @return array
	 */
	function getAllIClaMembersByLastName($last_name, $offset, $limit)
	{
		$query = new QueryObject;
		$query->addAddCondition(QueryCriteria::equal('CLASigned',true));
		$query->addAddCondition(QueryCriteria::like('Surname',$last_name));
		return $this->getAll($query, $offset, $limit);
	}
}