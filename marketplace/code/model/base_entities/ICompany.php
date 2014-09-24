<?php

/**
 * Interface ICompany
 */
interface ICompany extends IEntity  {

	public function getName();
	public function setName($name);

	public function getDescription();
	public function setDescription($description);

	public function getOverview();
	public function setOverview($overview);

	public function getCommitment();
	public function setCommitment($commitment);

	/**
	 * @return ITraining
	 */
	public function getDefaultTraining();

	/**
	 * @return ITraining[]
	 */
	public function getTrainings();



	/**
	 * @param IMarketPlaceType $type
	 * @return int
	 */
	public function getAllowedMarketplaceTypeInstances(IMarketPlaceType $type);

} 