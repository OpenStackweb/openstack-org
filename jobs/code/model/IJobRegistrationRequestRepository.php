<?php

/**
 * Interface IJobRegistrationRequestRepository
 */
interface IJobRegistrationRequestRepository
extends IEntityRepository {
	/**
	 * @param int $offset
	 * @param int $limit
	 * @return array
	 */
	public function getAllNotPostedAndNotRejected($offset = 0, $limit = 10);
} 