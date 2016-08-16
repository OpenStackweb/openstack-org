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
 * Class JobMainInfo
 */
final class JobMainInfo {
    /**
     * @return string
     */
    public function getInstructions2Apply()
    {
        return $this->instructions_2_apply;
    }

    /**
     * @return boolean
     */
    public function isCoaNeeded()
    {
        return $this->is_coa_needed;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

	/**
	 * @var string
	 */
	private $title;
	/**
	 * @var Company
	 */
	private $company;
	/**
	 * @var string
	 */
	private $url;
	/**
	 * @var string
	 */
	private $description;
	/**
	 * @var string
	 */
	private $instructions_2_apply;
	/**
	 * @var DateTime
	 */
	private $expiration_date;

	/**
	 * @var string
	 */
	private $location_type;

    /**
     * @var boolean
     */
    private $is_coa_needed;

    /**
     * @var string
     */
    private $type;

    /**
     * JobMainInfo constructor.
     * @param $title
     * @param $is_coa_needed
     * @param $type
     * @param $company
     * @param $url
     * @param $description
     * @param $instructions_2_apply
     * @param $location_type
     * @param null $expiration_date
     */
	public function __construct($title, $is_coa_needed, $type, $company, $url, $description, $instructions_2_apply, $location_type, $expiration_date = null){
		$this->title                = $title;
        $this->is_coa_needed        = $is_coa_needed;
        $this->type                 = $type;
		$this->company              = $company;
		$this->url                  = $url;
		$this->description          = $description;
		$this->instructions_2_apply = $instructions_2_apply;
		$this->location_type        = $location_type;
		$this->expiration_date      = $expiration_date;

	}

	/**
	 * @return string
	 */
	public function getTitle(){
		return $this->title;
	}

	/**
	 * @return Company
	 */
	public function getCompany(){
		return $this->company;
	}

	/**
	 * @return string
	 */
	public function getUrl(){
		return $this->url;
	}

	/**
	 * @return string
	 */
	public function getDescription(){
		return $this->description;
	}

	/**
	 * @return string
	 */
	public function getInstructions(){
		return $this->instructions_2_apply;
	}

	/**
	 * @return string
	 */
	public function getLocationType(){
		return $this->location_type;
	}

	/**
	 * @return DateTime|null
	 */
	public function getExpirationDate(){
		return $this->expiration_date;
	}
} 