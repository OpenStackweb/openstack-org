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
class CustomMySQLDatabase extends MySQLDatabase
{
    public function __construct($parameters)
    {
        parent::__construct($parameters);
        $this->query('SET AUTOCOMMIT=1;');
    }

    public function databaseError($msg, $errorLevel = E_USER_ERROR)
    {
        // try to extract and format query
        if(preg_match('/Couldn\'t run query: ([^\|]*)\|\s*(.*)/', $msg, $matches))
        {
            $formatter = new SQLFormatter();
            $msg = "Couldn't run query: \n" . $formatter->formatPlain($matches[1]) . "\n\n" . $matches[2];
        }
        $connect_errno = $this->dbConn->connect_errno;
        $errno         = $this->dbConn->errno;
        $sqlstate      = $this->dbConn->sqlstate;
        $error         = $this->dbConn->error;
        $connect_error = $msg;
        $msg           = sprintf
        (
            'connect_errno : %s - errno : %s - sqlstate : %s - error : %s - connect_error : %s',
            $connect_errno,
            $errno,
            $sqlstate,
            $error,
            $connect_error
        );
        SS_Log::log($msg, SS_Log::ERR);
        if(Director::get_environment_type() === "live")
        {
            ob_clean();
            $maintenance_page = file_get_contents(Director::baseFolder() . '/maintenance/index.html');
            echo $maintenance_page;
            header("HTTP/1.0 502 Bad Gateway");
            exit();
        }
        else
        {
            user_error($msg);
        }
    }

    public function transactionStart($transaction_mode=false, $session_characteristics=false){
        //By default, autocommit mode is enabled in MySQL.
        $this->query('SET AUTOCOMMIT=0;');
        parent::transactionStart($transaction_mode, $session_characteristics);
    }

    public function transactionRollback($savepoint = false){
        parent::transactionRollback($savepoint);
        $this->query('SET AUTOCOMMIT=1;');
    }

    public function query($sql, $errorLevel = E_USER_ERROR) {
        $query = parent::query($sql, $errorLevel);
        SS_Log::log($sql, SS_Log::DEBUG);
        return $query;
    }

}