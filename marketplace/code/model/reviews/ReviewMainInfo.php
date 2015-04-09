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
 * Class ReviewMainInfo
 */
final class ReviewMainInfo {

	/**
	 * @var string
	 */
	private $title;
    /**
     * @var string
     */
    private $comment;
    /**
     * @var int
     */
    private $rating;

	/**
	 * @param string $title
	 * @param string $comment
	 * @param int $rating
	 */
	public function __construct($title, $comment, $rating){
		$this->title                = $title;
		$this->comment              = $comment;
		$this->rating               = $rating;
	}

	/**
	 * @return string
	 */
	public function getTitle(){
		return $this->title;
	}

	/**
	 * @return string
	 */
	public function getComment(){
		return $this->comment;
	}

	/**
	 * @return int
	 */
	public function getRating(){
		return $this->rating;
	}
} 