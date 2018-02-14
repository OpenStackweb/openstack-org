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
 * Class ElectionVote
 */
final class ElectionVote
	extends DataObject
	implements IVote {

	static $db = [];

	static $has_one = [
		'Voter'    => 'Member',
		'Election' => 'Election',
	];

	/**
	 * @return int
	 */
	public function getIdentifier()
	{
		return (int)$this->getField('ID');
	}

	/**
	 * @return IFoundationMember
	 */
	public function voter()
	{
		return AssociationFactory::getInstance()->getMany2OneAssociation($this,'Voter')->getTarget();
	}

	/**
	 * @return IElection
	 */
	public function election()
	{
		return AssociationFactory::getInstance()->getMany2OneAssociation($this,'Election')->getTarget();
	}

	/**
	 * @return DateTime
	 */
	public function date()
	{
		// TODO: Implement date() method.
	}
}