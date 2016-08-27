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
 * Class JobLocation
 */
final class JobLocation
	extends DataObject
	implements IJobLocation {

	static $db = array
    (
		'City'          => 'Text',
		'State'         => 'Text',
		'Country'       => 'Text',
	);

	static $has_one = array(
		'Job'     => 'Job',
		'Request' => 'JobRegistrationRequest',
	);

	/**
	 * @return int
	 */
	public function getIdentifier()
	{
		return (int)$this->getField('ID');
	}

	/**
	 * @return string
	 */
	public function state()
	{
		return (string)$this->getField('State');
	}

	/**
	 * @return string
	 */
	public function city()
	{
		return (string)$this->getField('City');
	}

	/**
	 * @return string
	 */
	public function country()
	{
		return (string)$this->getField('Country');
	}
}