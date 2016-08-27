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
 * Class SearchDTO
 */
final class SearchDTO {
	/**
	 * @var string
	 */
	private $label;
	/**
	 * @var string
	 */
	private $value;

    private $id;

    /**
     * SearchDTO constructor.
     * @param $label
     * @param $value
     * @param null $id
     */
	public function __construct($label, $value, $id = null){
		$this->label = $label;
		$this->value = $value;
        $this->id    = $id;
	}

	/**
	 * @return string
	 */
	public function getLabel(){
		return $this->label;
	}

	/**
	 * @return string
	 */
	public function getValue(){
		return $this->value;
	}

    /**
     * @return string
     */
    public function getId(){
        return $this->id;
    }

} 