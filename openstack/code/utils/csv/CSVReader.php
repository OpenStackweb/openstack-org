<?php

/**
 * Class CSVReader
 */
final class CSVReader {

	/**
	 * @var resource
	 */
	private $file_handle;

	/**
	 * @param string $filename
	 * @throws InvalidArgumentException
	 */
	public function __construct($filename){
		if(!file_exists($filename))
			throw new InvalidArgumentException;
		$this->file_handle = fopen($filename, "r");
		if(!$this->file_handle)
			throw new InvalidArgumentException;
	}

	function __destruct() {
		fclose($this->file_handle);
	}

	/**
	 * @return array|bool
	 */
	function getLine(){
		if (!feof($this->file_handle) ) {
			return fgetcsv($this->file_handle, 1024);
		}
		return false;
	}
} 