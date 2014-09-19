<?php

/**
 * Interface IGerritAPI
 */
interface IGerritAPI {
	/**
	 * @param string $group_id
	 * @return array
	 */
	public function listAllMembersFromGroup($group_id);

	/**
	 * @param string $gerrit_user_id
	 * @return DateTime
	 */
	public function getUserLastCommit($gerrit_user_id);
} 