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
 * Class PricingSchemaType
 */
class PricingSchemaType extends DataObject implements IPricingSchemaType {


	static $db = [
		'Type'  => 'Varchar',
	];

	static $indexes = [
		'Type' => array('type'=>'unique', 'value'=>'Type')
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

	public function setType($type)
	{
		$this->setField('Type',$type);
	}

	public function getType()
	{
		return $this->getField('Type');
	}
}