<?php

/**
 * Interface ICLAMemberRepository
 */
interface ICLAMemberRepository extends IMemberRepository {

	/***
	 * @return int[]
	 */
	function getAllGerritIds();

	/**
	 * @param int $offset
	 * @param int $limit
	 * @return ICLAMember[]
	 */
	function getAllICLAMembers($offset, $limit);

	/**
	 * @param string $email
	 * @param int $offset
	 * @param int $limit
	 * @return array
	 */
	function getAllIClaMembersByEmail($email, $offset, $limit);

	/**
	 * @param string $first_name
	 * @param int $offset
	 * @param int $limit
	 * @return array
	 */
	function getAllIClaMembersByFirstName($first_name, $offset, $limit);

	/**
	 * @param string $last_name
	 * @param int $offset
	 * @param int $limit
	 * @return array
	 */
	function getAllIClaMembersByLastName($last_name, $offset, $limit);

} 