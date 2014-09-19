<?php

/**
 * Interface IMemberRepository
 */
interface IMemberRepository extends IEntityRepository {
	/**
	 * @param string $email
	 * @return ICLAMember
	 */
	public function findByEmail($email);
} 