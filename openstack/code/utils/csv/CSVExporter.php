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
 * Class CSVExporter
 */
final class CSVExporter
{

    /**
     * @var CSVExporter
     */
    private static $instance;

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    /**
     * @return CSVExporter
     */
    public static function getInstance()
    {
        if (!is_object(self::$instance)) {
            self::$instance = new CSVExporter();
        }
        return self::$instance;
    }


    /**
     * @param        $filename
     * @param array  $data
     * @param string $field_separator
     * @param string $mime_type
     */
    public function export($filename, array $data, $field_separator = "\t", $mime_type = 'application/vnd.ms-excel')
    {
        ob_end_clean();
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        header('Content-Type: ' . $mime_type);
        header('Content-Transfer-Encoding: binary');
        header('Accept-Ranges: bytes');
        header('Cache-control: no-cache, pre-check=0, post-check=0');
        header('Cache-control: private');
        header('Pragma: private');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // any date in the past

        $flag = false;
        foreach ($data as $row) {
            if (!$flag) {
                // display field/column names as first row
                $header = array_keys($row);
                array_walk($header, array($this, 'cleanData'));
                $header = implode($field_separator, $header) . "\n";
                echo $header;
                $flag = true;
            }
            array_walk($row, array($this, 'cleanData'));
            $line = implode($field_separator, array_values($row)) . "\n";
            echo $line;
        }
        exit;
    }

    function cleanData(&$str)
    {
        if (is_null($str)) return '';
        $str = preg_replace("/\t/", "\\t", $str);
        $str = preg_replace("/\r?\n/", "\\n", $str);
        $str = preg_replace("/,/", "-", $str);
        if (strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
    }
} 