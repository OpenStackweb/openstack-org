<?php

/**
 * Class SessionCacheService
 */
class SessionCacheService implements ICacheService {

	/**
	 * @param $key
	 * @return mixed
	 */
	public function getSingleValue($key)
	{
		return Session::get($key);
	}

	/**
	 * @param string $key
	 * @param object $value
	 * @return void
	 */
	public function setSingleValue($key, $value)
	{
		Session::clear($key);
		Session::set($key,$value);
	}
}