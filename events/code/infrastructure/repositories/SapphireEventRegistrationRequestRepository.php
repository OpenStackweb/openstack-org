<?php

/**
 * Class SapphireEventRegistrationRequestRepository
 */
final class SapphireEventRegistrationRequestRepository
	extends SapphireRepository implements IEventRegistrationRequestRepository{

	public function __construct(){
		parent::__construct(new EventRegistrationRequest);
	}

	public function getAllNotPostedAndNotRejected($offset = 0, $limit = 10) {
		$query = new QueryObject();
		$query->addAddCondition(QueryCriteria::equal('isPosted', 0));
		$query->addAddCondition(QueryCriteria::equal('isRejected', 0));
		return  $this->getAll($query,$offset,$limit);
	}
} 