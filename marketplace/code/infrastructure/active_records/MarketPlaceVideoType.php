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
 * Class MarketPlaceVideoType
 */
class MarketPlaceVideoType extends DataObject implements IMarketPlaceVideoType {


	static $db = [
		'Type'               => 'Varchar',
		'Title'              => 'Text',
		'Description'        => 'Text',
		'MaxTotalVideoTime'  => 'int',
	];

	static $indexes = [
		'Type' => ['type'=>'unique', 'value'=>'Type']
	];

	static $summary_fields = [
		'Type' => 'Type',
	];

	/**
	 * @return int
	 */
	public function getIdentifier()
	{
		return (int)$this->getField('ID');
	}

	public function getType()
	{
		return $this->getField('Type');
	}

	public function setType($type)
	{
		$this->setField('Type',$type);
	}

	public function getMaxTotalTime()
	{
		return (int)$this->getField('MaxTotalVideoTime');
	}

	public function setMaxTotalTime($time)
	{
		$this->setField('MaxTotalVideoTime',$time);
	}
}