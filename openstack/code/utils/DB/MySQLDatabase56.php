<?php
/**
 * Copyright 2015 OpenStack Foundation
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
 * Class MySQLDatabase56
 * support for fractional seconds
 * http://dev.mysql.com/doc/refman/5.6/en/fractional-seconds.html
 */
class MySQLDatabase56 extends CustomMySQLDatabase {

    const MicrosecondsPrecision = 6;

    public function __construct($parameters) {
        parent::__construct($parameters);
    }
    /**
     * Return a datetime type-formatted string
     * For MySQL, we simply return the word 'datetime', no other parameters are necessary
     *
     * @param array $values Contains a tokenised list of info about this data type
     * @return string
     */
    public function ss_datetime($values){
        //For reference, this is what typically gets passed to this function:
        //$parts=Array('datatype'=>'datetime');
        //DB::requireField($this->tableName, $this->name, $values);

        return "datetime(".self::MicrosecondsPrecision.")";
    }

    /**
     * function to return an SQL datetime expression that can be used with MySQL
     * used for querying a datetime in a certain format
     * @param string $date to be formated, can be either 'now', literal datetime like '1973-10-14 10:30:00' or
     * field name, e.g. '"SiteTree"."Created"'
     * @param string $format to be used, supported specifiers:
     * %Y = Year (four digits)
     * %m = Month (01..12)
     * %d = Day (01..31)
     * %H = Hour (00..23)
     * %i = Minutes (00..59)
     * %s = Seconds (00..59)
     * %U = unix timestamp, can only be used on it's own
     * @return string SQL datetime expression to query for a formatted datetime
     */
    public function formattedDatetimeClause($date, $format) {

        preg_match_all('/%(.)/', $format, $matches);
        foreach($matches[1] as $match) if(array_search($match, array('Y','m','d','H','i','s','U')) === false) {
            user_error('formattedDatetimeClause(): unsupported format character %' . $match, E_USER_WARNING);
        }

        if(preg_match('/^now$/i', $date)) {
            $date = "NOW(".self::MicrosecondsPrecision.")";
        } else if(preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/i', $date)) {
            $date = "'$date'";
        }

        if($format == '%U') return "UNIX_TIMESTAMP($date)";

        return "DATE_FORMAT($date, '$format')";

    }

    /**
     * Return a time type-formatted string
     * For MySQL, we simply return the word 'time', no other parameters are necessary
     *
     * @param array $values Contains a tokenised list of info about this data type
     * @return string
     */
    public function time($values){
        //For reference, this is what typically gets passed to this function:
        //$parts=Array('datatype'=>'time');
        //DB::requireField($this->tableName, $this->name, "time");

        return 'time('.self::MicrosecondsPrecision.')';
    }


    public function now(){
        return 'NOW('.self::MicrosecondsPrecision.')';
    }

    /**
     * @return string
     */
    public static function nowRfc2822(){
        list($usec, $sec) = explode(' ', microtime());
        $usec             = substr($usec, 2, self::MicrosecondsPrecision);
        return date('Y-m-d H:i:s', $sec).'.'.$usec;
    }
}