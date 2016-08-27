<?php
/**
 * Copyright 2014 Openstack Foundation
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 **/
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
	public function __construct($filename = null){
		if(is_null($filename)) return;
		if(!file_exists($filename))
			throw new InvalidArgumentException;
		$this->file_handle = fopen($filename, "r");
		if(!$this->file_handle)
			throw new InvalidArgumentException;
	}

    /**
     * @param string $content
     * @return array
     */
    public static function load($content)
    {
        $data    = str_getcsv($content,"\n"  );
        $lines   = array();
        $header  = array();
        $idx     = 0;
        foreach($data as $row)
        {
            $row = str_getcsv($row, ",");
            ++$idx;
            if($idx === 1) { $header = $row; continue;}
            $line  = array();
            for($i=0; $i < count($header); $i++){
                $line[$header[$i]] = trim($row[$i]);
            }
            $lines[] = $line;

        } //parse the items in rows
        return $lines;
    }

	function __destruct() {
        if($this->file_handle) fclose($this->file_handle);
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