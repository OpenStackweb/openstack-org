<?php

/**
 * Class AbstractRestfulServiceRequestor
 */
abstract class AbstractRestfulServiceRequestor {

	/**
	 * @param string $url
	 * @param null|string $user
	 * @param null|string $password
	 * @return mixed
	 */
	protected function doRequest($url, $user = null, $password = null){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		if(!empty($user) && !empty($password)){
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
			curl_setopt($ch, CURLOPT_USERPWD, sprintf("%s:%s", $user, $password));
		}
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		$response = curl_exec($ch);
		curl_close($ch);
		return $response;
	}
} 