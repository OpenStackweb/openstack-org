<?php

/**
 * Class MarketPlaceRestfulApi
 */
abstract class MarketPlaceRestfulApi extends AbstractRestfulJsonApi {

	const ApiPrefix = 'api/v1/marketplace';

	protected function isApiCall(){
		$request = $this->getRequest();
		if(is_null($request)) return false;
		return  strpos(strtolower($request->getURL()),self::ApiPrefix) !== false;
	}

	/**
	 * @return bool
	 */
	protected function authorize(){
		//check permissions
		if(!$this->current_user->isMarketPlaceAdmin())
			return false;
		return true;
	}

	/**
	 * @param QueryObject $query
	 * @param             $get
	 * @param             $convert_to_array
	 * @return SS_HTTPResponse
	 */
	protected function getAll(QueryObject $query, $get, $convert_to_array){
		try{
			$offset = is_null($this->request->getVar('offset'))? 0 :$this->request->getVar('offset');
			$limit  = is_null($this->request->getVar('limit')) ? 10:$this->request->getVar('limit');

			list($list,$size)   = call_user_func($get ,$query,$offset,$limit);
			$res    = array();
			foreach($list as $entity){
				array_push($res , call_user_func( $convert_to_array, $entity));
			}
			return $this->ok(array(
				'items'       => $res,
				'page'        => $offset,
				'page_size'   => $limit,
				'total_count' => $size
			));
		}
		catch(Exception $ex){
			return $this->serverError();
		}
	}
}