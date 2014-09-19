<?php

abstract class AbstractController extends Controller {
	/**
	 * Determine if the request is sending JSON.
	 *
	 * @return bool
	 */
	protected function isJson()
	{
		$content_type_header = $this->request->getHeader('Content-Type');
		if(empty($content_type_header)) return false;
		return strpos($content_type_header, '/json')!==false;
	}

} 