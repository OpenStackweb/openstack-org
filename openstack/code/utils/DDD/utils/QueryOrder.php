<?php

/**
 * Class QueryOrder
 */
final class QueryOrder {

	private $field;
	private $dir;

	private function __construct($field,$dir){
		$this->field    = $field;
		$this->dir = $dir;
	}

	public function getField(){
		return $this->field;
	}

	public function getDir(){
		return $this->dir;
	}

	public static function asc($field){
		return new QueryOrder($field,'ASC');
	}

	public static function desc($field){
		return new QueryOrder($field,'DESC');
	}

	public function __toString()
	{
		$field = $this->field;
		if(strpos($field,'.')){
			$parts = explode('.',$field);
			$parsed_field = '';
			foreach($parts as $part){
				$parsed_field .= sprintf('`%s`.',$part);
			}
			$field = trim($parsed_field,'.');
		}
		else{
			$field = sprintf('`%s`',$field);
		}
		return sprintf(" %s %s ",$field,$this->dir);
	}
}