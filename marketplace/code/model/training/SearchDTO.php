<?php

/**
 * Class SearchDTO
 */
final class  SearchDTO {
	/**
	 * @var string
	 */
	private $label;
	/**
	 * @var string
	 */
	private $value;

	/**
	 * @param string $label
	 * @param string $value
	 */
	public function __construct($label, $value){
		$this->label = $label;
		$this->value = $value;
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

} 