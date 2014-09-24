<?php

/**
 * Class ArrayUtils
 */
final class ArrayUtils {

	/**
	 * @param array $in
	 * @param array|null  $default_string
	 * @param array|null  $other
	 * @return array
	 */
	public static function AlphaSort (array $in, array $default_string = null, array $other=null) {
		array_multisort($in, SORT_STRING);
		if(!is_null($default_string) && is_array($default_string)) {
			$in = array_merge($default_string, $in);
		}
		if(!is_null($other) && is_array($other)){
			$in = array_merge($in, $other);
		}
		return $in;
	}
} 