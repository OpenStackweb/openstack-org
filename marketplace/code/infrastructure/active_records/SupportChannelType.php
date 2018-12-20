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
 * Class SupportChannelType
 */
class SupportChannelType extends DataObject implements ISupportChannelType {

	static $create_table_options = array('MySQLDatabase' => 'ENGINE=InnoDB');

	static $db = array(
		'Type' => 'Varchar',
	);

	static $indexes = array(
		'Type' => array('type'=>'unique', 'value'=>'Type')
	);

	static $summary_fields = array(
		'Type' => 'Type',
	);

	static $has_one = array(
		'Icon' => 'CloudImage',
	);

	/**
	 * @return int
	 */
	public function getIdentifier()
	{
		return (int)$this->getField('ID');
	}

	/**
	 * @param string $type
	 * @return void
	 */
	public function setType($type)
	{
		$this->setField('Type',$type);
	}

	/**
	 * @return string
	 */
	public function getType()
	{
		return $this->getField('Type');
	}

	public function getInfo()
	{
		return $this->getField('Data');
	}
}