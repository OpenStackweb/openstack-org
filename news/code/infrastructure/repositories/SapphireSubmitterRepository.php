<?php

/**
 * Class SapphireSubmitterRepository
 */
final class SapphireSubmitterRepository extends SapphireRepository {

	public function __construct(){
        parent::__construct(new Submitter());
	}

    /**
     * @return ISubmitter
     */
    public function getSubmitterByEmail($email)
    {
        $query = new QueryObject(new Submitter);
        $query->addAddCondition(QueryCriteria::equal('Email',$email));
        return $this->getBy($query);
    }

}