<?php

/**
 * Interface IVoterFileRepository
 */
interface  IVoterFileRepository extends IEntityRepository {
	/**
	 * @param string $filename
	 * @return IVoterFile
	 */
	public function getByFileName($filename);
} 