<?php

/**
 * Class QueryAlias
 */
final class QueryAlias {

	private $sub_alias = array();
	private $name;
	private $field;

	private function __construct($name, $field = null){
		$this->name  = $name;
		$this->field = is_null($field)?$name.'ID':$field;
	}
	private function __clone(){}

	public static function create($name, $field = null){
		$instance = new QueryAlias($name, $field);
		return $instance;
	}

	public function addAlias(QueryAlias $sub_alias){
		array_push($this->sub_alias,$sub_alias);
		return $this;
	}

	public function getName(){
		return $this->name;
	}

	public function getField(){
		return $this->field;
	}

	public function hasSubAlias(){
		return count($this->sub_alias) > 0;
	}

	public function subAlias(){
		$join = array();

		foreach($this->sub_alias as $alias){
			$class_name = ClassInfo::baseDataClass($this->name);
			$base_entity = singleton($this->name);
			$child      = $alias->getName();

			$has_many   = Config::inst()->get($class_name, 'has_many');
			if(!is_null($has_many)){
				$has_many_classes = array_flip($has_many);

				if(array_key_exists($child,$has_many_classes)){
					$joinField = $base_entity->getRemoteJoinField($has_many_classes[$child], 'has_many');
					$join[$child] = $child.'.'.$joinField.' = '.$class_name.'.ID';
				}
			}


			$has_one         = Config::inst()->get($class_name, 'has_one');
			if(!is_null($has_one)){
				$has_one_classes = array_flip($has_one);
				if(array_key_exists($child,$has_one_classes)){
					$join[$child] = $child.'.ID = '.$class_name.'.'.$has_one_classes[$child].'ID';
				}
			}

			if($alias->hasSubAlias()){
				$join = array_merge($join, $alias->subAlias());
			}
		}
		return $join;
	}
}