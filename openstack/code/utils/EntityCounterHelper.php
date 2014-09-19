<?php

/**
 * Class EntityCounterHelper
 */
final class EntityCounterHelper {
	/**
	 * @var EntityCounterHelper
	 */
	private static $instance;

	private function __construct(){}

	private function __clone(){}

	/**
	 * @return EntityCounterHelper
	 */
	public static function getInstance(){
		if(!is_object(self::$instance)){
			self::$instance = new EntityCounterHelper();
		}
		return self::$instance;
	}

	public function EntityCount($entity_name, callable $payload_function=null){
		$cache  = SS_Cache::factory('cache_entity_count');
		$result = unserialize($cache->load('var_'.$entity_name));
		if(!$result){
			if($payload_function==null){
				$sqlQuery = new SQLQuery(
					"COUNT(ID)",
					array($entity_name)
				);
				$result = $sqlQuery->execute()->value();
			}
			else{
				$result = $payload_function();
			}
			$cache->save(serialize($result), 'var_'.$entity_name);
		}

		if(Director::is_ajax()){
			return json_encode($result);
		}
		return $result;
	}
} 