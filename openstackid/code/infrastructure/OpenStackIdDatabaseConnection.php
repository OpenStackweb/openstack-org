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
 * Class OpenStackIdDatabaseConnection
 * Wrapper that emulates PEAR connection functionality which is needed for JanRain Lib
 */
class OpenStackIdDatabaseConnection extends Auth_OpenID_DatabaseConnection {

    public function query($sql, $params = array()) {
        if(($sql = $this->generateQuery($sql, $params)) === false)
            return false;

        return DB::query($sql);
    }

    public function getOne($sql, $params = array()) {
        if(($sql = $this->generateQuery($sql, $params)) === false)
            return false;

        if(($result = DB::query($sql)) === false)
            return false;

        return $result->value();
    }

    public function getRow($sql, $params = array()) {
        if(($sql = $this->generateQuery($sql, $params)) === false)
            return false;

        if(($result = DB::query($sql)) === false)
            return false;

        return $result->record();
    }

    public function getAll($sql, $params = array()) {

        if(($sql = $this->generateQuery($sql, $params)) === false)
            return false;

        if(($result = DB::query($sql)) === false)
            return false;

        for($result_array = array(); $result->valid(); $result->next()) {
            array_push($result_array, $result->current());
        }

        return $result_array;
    }

    public function autoCommit($mode) {
        $mode = $mode == false? 0:1;
        DB::query("SET AUTOCOMMIT={$mode};");
    }

    function begin()
    {
        DB::getConn()->transactionStart();
    }


    public function commit() {
        DB::getConn()->transactionEnd();
    }

    public function rollback() {
        DB::getConn()->transactionRollback();
    }

    private function generateQuery($sql, $params = array()) {
        $tokens   = preg_split('/((?<!\\\)[&?!])/', $sql, -1,
            PREG_SPLIT_DELIM_CAPTURE);
        $token     = 0;
        $types     = array();
        $newtokens = array();

        foreach ($tokens as $val) {
            switch ($val) {
                case '?':
                    $types[$token++] = 'SCALAR';
                    break;
                case '!':
                    $types[$token++] = 'MISC';
                    break;
                default:
                    $newtokens[] = preg_replace('/\\\([&?!])/', "\\1", $val);
            }
        }

        if(count($types) != count($params))
            return false;


        $realquery = $newtokens[0];
        $i = 0;

        foreach($params as $value) {
            if($types[$i] == 'SCALAR') {
                $realquery .= $this->quote($value);
            } else {
                $realquery .= $value;
            }

            $realquery .= $newtokens[++$i];
        }

        return $realquery;
    }

    private function quote($in) {
        if(is_int($in)) {
            return $in;
        } elseif(is_float($in)) {
            return "'" . Convert::raw2sql(
                str_replace(',', '.', strval(floatval($in)))) . "'";
        } elseif(is_bool($in)) {
            return ($in) ? '1' : '0';
        } elseif(is_null($in)) {
            return 'NULL';
        } else {
            return "'" . Convert::raw2sql($in) . "'";
        }
    }
}