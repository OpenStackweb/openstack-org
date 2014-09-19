<?php

/**
 * Class SapphireVoterFileRepository
 */
final class SapphireVoterFileRepository extends SapphireRepository implements IVoterFileRepository {

	public function __construct(){
		parent::__construct(new VoterFile());
	}

	/**
	 * @param string $filename
	 * @return IVoterFile
	 */
	public function getByFileName($filename)
	{
		$query = new QueryObject(new VoterFile);
		$query->addAddCondition(QueryCriteria::equal('FileName',$filename));
		return $this->getBy($query);
	}
}