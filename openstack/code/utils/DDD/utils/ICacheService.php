<?php

interface ICacheService {
	/**
	 * @param $key
	 * @return mixed
	 */
	public function getSingleValue($key);

	/**
	 * @param string $key
	 * @param object $value
	 * @return void
	 */
	public function setSingleValue($key,$value);
} 