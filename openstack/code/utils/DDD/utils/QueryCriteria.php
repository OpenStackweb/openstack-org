<?php

/**
 * Class QueryCriteria
 */
final class QueryCriteria {

	private $field;
	private $operator;
	private $value;
	private $quoted;

	/**
	 * @param string $field
	 * @param string $operator
	 * @param object $value
	 * @param bool $quoted
	 */
	private function __construct($field,$operator,$value, $quoted = true){
		$this->field    = $field;
		$this->operator = $operator;
		$this->value    = $value;
		$this->quoted   = $quoted;
	}

	public function getField(){
		return $this->field;
	}

	public function getOperator(){
		return $this->operator;
	}

	public function getValue(){
		return $this->value;
	}

	public static function equal($field,$value,$quoted = true){
		return new QueryCriteria($field,'=',$value,$quoted);
	}

	public static function notEqual($field,$value,$quoted = true){
		return new QueryCriteria($field,'<>',$value,$quoted);
	}

	public static function greater($field,$value,$quoted = true){
		return new QueryCriteria($field,'>',$value,$quoted);
	}

	public static function greaterOrEqual($field,$value,$quoted = true){
		return new QueryCriteria($field,'>=',$value,$quoted);
	}

	public static function lower($field,$value,$quoted = true){
		return new QueryCriteria($field,'<',$value,$quoted);
	}

	public static function lowerOrEqual($field,$value,$quoted = true){
		return new QueryCriteria($field,'<=',$value,$quoted);
	}

	public static function like($field,$value,$quoted = true){
		return new QueryCriteria($field,'LIKE','%'.$value.'%',$quoted);
	}

	public static function isNull($field,$quoted = true){
		return new QueryCriteria($field,'IS',"NULL",$quoted);
	}

	public function __toString()
	{
		$field = $this->field;

		if(strpos($field,'ID')>0)
			$field = str_replace('.','',$field);

		if(strpos($field,'.')){
			$parts = explode('.',$field);
			$parsed_field = '';
			foreach($parts as $part){
				if($this->quoted)
					$parsed_field .= sprintf('`%s`.',$part);
				else
					$parsed_field .= sprintf('%s.',$part);
			}
			$field = trim($parsed_field,'.');
		}
		else{
			$field = sprintf('%s',$field);
			if($this->quoted)
				$field = '`'.$field.'`';
		}

		return is_string($this->value) && $this->value!=='NULL' ?
			sprintf(" %s %s '%s' ",$field,$this->operator,Convert::raw2sql($this->value)):
			sprintf(" %s %s %s ",  $field,$this->operator,Convert::raw2sql($this->value));
	}
} 