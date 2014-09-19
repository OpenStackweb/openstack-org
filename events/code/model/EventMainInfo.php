<?php

/**
 * Class EventMainInfo
 */
final class EventMainInfo {
	/**
	 * @var string
	 */
	private $title;
	/**
	 * @var string
	 */
	private $url;
	/**
	 * @var string
	 */
	private $label;

	/**
	 * @param string $title
	 * @param string $url
	 * @param string $label
	 */
	public function __construct($title,$url,$label){
		$this->title = $title;
		$this->url   = $url;
		$this->label = $label;
	}

	public function getTitle(){
		return $this->title;
	}

	public function getUrl(){
		return $this->url;
	}

	public function getLabel(){
		return $this->label;
	}
} 