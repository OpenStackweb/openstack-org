<?php
class FileUtils{
	public static function convertToFileName($name){
		$bad='/[\/:*?"<>|]\.#@!¬|,\"/';
		$file = str_replace(' ', '_', strtolower($name));
		$file = preg_replace($bad,"",$file);
		return $file;
	}
	
}