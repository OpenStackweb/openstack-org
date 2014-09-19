<?php
/**
 * Interface IEventRegistrationRequestRepository
 */
interface IEventRegistrationRequestRepository
	extends IEntityRepository {
	/**
	 * @param int $offset
	 * @param int $limit
	 * @return array
	 */
	public function getAllNotPostedAndNotRejected($offset = 0, $limit = 10);
} 