<?php

/**
 * Class CSVExporter
 */
final class CSVExporter {

	/**
	 * @var CSVExporter
	 */
	private static $instance;

	private function __construct(){}

	private function __clone(){}

	/**
	 * @return CSVExporter
	 */
	public static function getInstance(){
		if(!is_object(self::$instance)){
			self::$instance = new CSVExporter();
		}
		return self::$instance;
	}


	/**
	 * @param string $filename
	 * @param array  $data
	 */
	public function export($filename, array $data){
		//clean output buffer
		ob_end_clean();
		header("Content-Disposition: attachment; filename=\"$filename\"");
		header("Content-Type: application/vnd.ms-excel");

		$flag = false;
		foreach($data as $row) {
			if(!$flag) {
				// display field/column names as first row
				echo implode("\t", array_keys($row)) . "\n";
				$flag = true;
			}
			array_walk($row, array($this,'cleanData'));
			echo implode("\t", array_values($row)) . "\n";
		}
	}

	function cleanData(&$str)
	{
		$str = preg_replace("/\t/", "\\t", $str);
		$str = preg_replace("/\r?\n/", "\\n", $str);
		if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
	}
} 