<?php
/**
 * Class SapphireJobRegistrationRequestRepository
 */
final class SapphireJobRegistrationRequestRepository
extends SapphireRepository
implements IJobRegistrationRequestRepository
{

	public function __construct(){
		parent::__construct(new JobRegistrationRequest);
	}

	/**
	 * @param int $offset
	 * @param int $limit
	 * @return array
	 */
	public function getAllNotPostedAndNotRejected($offset = 0, $limit = 10)	{
		$query = new QueryObject();
		$query->addAddCondition(QueryCriteria::equal('isPosted', 0));
		$query->addAddCondition(QueryCriteria::equal('isRejected', 0));
		return  $this->getAll($query,$offset,$limit);
	}
}