<?php
/**
 * Copyright 2018 OpenStack Foundation
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
class CustomMySQLDatabase extends MySQLDatabase
{
    const MicrosecondsPrecision = 6;
    const DefaultWebServerTimeZone = 'America/Chicago';

    public function __construct()
    {
    }

    public function connect($parameters)
    {
        // Ensure that driver is available (required by PDO)
        if(empty($parameters['driver'])) {
            $parameters['driver'] = $this->getDatabaseServer();
        }

        // Set charset
        if( empty($parameters['charset'])
            && ($charset = Config::inst()->get('MySQLDatabase', 'connection_charset'))
        ) {
            $parameters['charset'] = $charset;
        }

        // Set collation
        if( empty($parameters['collation'])
            && ($collation = Config::inst()->get('MySQLDatabase', 'connection_collation'))
        ) {
            $parameters['collation'] = $collation;
        }

        // Notify connector of parameters
        $this->connector->connect($parameters);

        // This is important!
        //$this->setSQLMode('ANSI');
        //omit ONLY_FULL_GROUP_BY which became part of the mysql 5.7.5 combo 'ANSI'
        // @see https://dev.mysql.com/doc/refman/5.7/en/sql-mode.html#sql-mode-combo
        $this->setSQLMode("REAL_AS_FLOAT,PIPES_AS_CONCAT,ANSI_QUOTES,IGNORE_SPACE");

        if (isset($parameters['timezone'])) {
            $this->selectTimezone($parameters['timezone']);
        }

        // SS_Database subclass maintains responsibility for selecting database
        // once connected in order to correctly handle schema queries about
        // existence of database, error handling at the correct level, etc
        if (!empty($parameters['database'])) {
            $this->selectDatabase($parameters['database'], false, false);
        }
        $this->query('SET AUTOCOMMIT=1;');
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
    /**
     * @return string
     */
    public function now(){
        //return 'NOW('.self::MicrosecondsPrecision.')';
        // todo: this is a kludge due this bug https://github.com/silverstripe/silverstripe-framework/issues/6848
        $web_server_time_zone = Config::inst()->get('MySQLDatabase', 'web_server_time_zone');
        if(empty($web_server_time_zone))
            $web_server_time_zone = self::DefaultWebServerTimeZone;
        return sprintf("CONVERT_TZ(NOW(%s), @@system_time_zone, '%s')", self::MicrosecondsPrecision, $web_server_time_zone);
    }
    /**
     * @return string
     */
    public static function nowRfc2822(){
        list($usec, $sec) = explode(' ', microtime());
        $usec             = substr($usec, 2, self::MicrosecondsPrecision);
        return gmdate('Y-m-d H:i:s', $sec).'.'.$usec;
    }

    // tx

    public function transactionStart($transaction_mode=false, $session_characteristics=false){
        //By default, autocommit mode is enabled in MySQL.
        $this->query('SET AUTOCOMMIT=0;');
        parent::transactionStart($transaction_mode, $session_characteristics);
    }

    public function transactionRollback($savepoint = false){
        parent::transactionRollback($savepoint);
        $this->query('SET AUTOCOMMIT=1;');
    }

    /*
	 * Commit everything inside this transaction so far
	 */
    public function transactionEnd($chain = false){
        parent::transactionEnd($chain);
        $this->query('SET AUTOCOMMIT=1;');
    }

}