<?php
/**
 * Copyright 2014 Openstack Foundation
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 **/
/**
 * Class SapphireFoundationMemberRepository
 */
final class SapphireFoundationMemberRepository
	extends SapphireRepository
	implements IFoundationMemberRepository{


	public function __construct(){
		$entity = new FoundationMember();
		$entity->setOwner(new Member());
		parent::__construct($entity);
	}

	/**
	 * @param int                 $n
	 * @param int                 $limit
	 * @param int                 $offset
	 * @param IElectionRepository $election_repository
	 * @return int[]
	 */
	public function getMembersThatNotVotedOnLatestNElections($n, $limit, $offset, IElectionRepository $election_repository)
	{
		$specification = new FoundationMembershipRevocationSpecification;
		$sql           = $specification->sql($n, $necessary_votes = 2 , $election_repository, $offset, $limit);
        if(Director::is_cli()){
            fwrite
            (
                STDOUT,
                sprintf
                (
                    "%s - [SapphireFoundationMemberRepository::getMembersThatNotVotedOnLatestNElections] sql %s",
                    gmdate('Y-m-d h:i:s \G\M\T', time()),
                    $sql
                ).PHP_EOL
            );
        }
		$res           = DB::query($sql);
		$list          = [];

		foreach ($res as $record) {
			$list[] = (int)$record["ID"];
		}
		return $list;
	}

	/**
	 * @param string $first_name
	 * @param string $last_name
	 * @return IFoundationMember[]
	 */
	public function getByCompleteName($first_name, $last_name)
	{
		$query = new QueryObject(new Member());
		$query->addAndCondition(QueryCriteria::equal('FirstName',$first_name));
		$query->addAndCondition(QueryCriteria::equal('Surname',$last_name));
		list($res, $count) = $this->getAll($query, 0, PHP_INT_MAX);
        return $res;
	}
}