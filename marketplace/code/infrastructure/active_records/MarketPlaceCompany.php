<?php

/**
 * Class MarketPlaceCompany
 */
class MarketPlaceCompany extends DataExtension implements ICompany {

	static $has_many =  array(
	'MarketPlaceTypeAllowedInstances' => 'MarketPlaceAllowedInstance',
	);

	public function getName()
	{
		return $this->owner->getField('Name');
	}

	public function setName($name)
	{
		$this->owner->setField('Name',$name);
	}

	public function getDescription()
	{
		return $this->owner->getField('Description');
	}

	public function setDescription($description)
	{
		$this->owner->setField('Description',$description);
	}

	public function getOverview()
	{
		return $this->owner->getField('Overview');
	}

	public function setOverview($overview)
	{
		$this->owner->setField('Overview',$overview);
	}

	public function getCommitment()
	{
		return $this->owner->getField('Commitment');
	}

	public function setCommitment($commitment)
	{
		$this->owner->setField('Commitment',$commitment);
	}

	public function getIdentifier()
	{
		return (int)$this->owner->getField('ID');
	}

	/**
	 * @return ITraining|void
	 * @throws Exception
	 */
	public function getDefaultTraining()
	{
		$trainings = $this->getTrainings();
		if (!$trainings || $trainings->Count() == 0)
			throw new Exception("There are not available trainings");
		$training = $trainings->First();
		//get associated courses (incoming ones)
		$courses  = $training->Courses();
		if (!$courses || $courses->Count() == 0)
			throw new Exception("There are not available incoming courses");
		return $training;
	}

	/**
	 * @return ITraining[]
	 */
	public function getTrainings(){

		$query = new QueryObject;
		$query->addAddCondition(QueryCriteria::equal('ClassName','TrainingService'));
		$query->addAddCondition(QueryCriteria::equal('Active',true));
		$query = (string)$query;
		return $this->owner->Services($query);
	}

	/**
	 * @param IMarketPlaceType $type
	 * @return int
	 */
	public function getAllowedMarketplaceTypeInstances(IMarketPlaceType $type)
	{
		$mkt = $this->owner->MarketPlaceTypeAllowedInstances('MarketPlaceTypeID = '.$type->getIdentifier())->first();
		return is_null($mkt)?1:$mkt->getMaxInstances();
	}
}