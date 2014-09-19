<?php
/**
 * Class SponsorInfo
 */
final class SponsorInfo {
	/**
	 * @var string
	 */
	private $name;
	/**
	 * @var string
	 */
	private $url;

	/**
	 * @param string $name
	 * @param string $url
	 */
	public function __construct($name, $url){
		$this->name = $name;
		$this->url  = $url;
	}

	/**
	 * @return string
	 */
	public function getName(){
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function getUrl(){
		return $this->url;
	}
} 