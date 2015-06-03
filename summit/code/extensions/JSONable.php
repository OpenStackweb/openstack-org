<?php

class JSONable extends DataExtension
{

	public function toJSON($inherited = false, $has_one = true) {
		$flag = $inherited ? Config::INHERITED : Config::UNINHERITED;
		$db = Config::inst()->get($this->owner->class, "db", $flag);
		$data = array ();
		foreach($db as $field => $type) {
			$data[self::to_underscore($field)] = $this->owner->$field;
		}

		foreach($this->owner->has_one() as $relation => $class) {
			$fieldName = "{$relation}ID";
			$data[self::to_underscore("{$relation}ID")] = $this->owner->$fieldName;
		}

		$data['id'] = $this->owner->ID;

		return $data;
	}


	public static function to_underscore($str) {
		$str = preg_replace('/ID$/', 'Id', $str);
		$str[0] = strtolower($str[0]);		
		return preg_replace_callback('/([A-Z])/', function ($c) {
			return "_" . strtolower($c[1]);
		}, $str);
	}
}