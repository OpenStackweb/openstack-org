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
class CustomMySQLSchemaManager extends MySQLSchemaManager
{
    public function int($values){
        //For reference, this is what typically gets passed to this function:
        //$parts=Array('datatype'=>'int', 'precision'=>11, 'null'=>'not null', 'default'=>(int)$this->default);
        //DB::requireField($this->tableName, $this->name, "int(11) not null default '{$this->defaultVal}'");
        $precision = isset($values['precision']) ? intval($values['precision']): 11;
        $nullable  = isset($values['null']) ? ( $values['null'] === 'null' ? 'NULL':'NOT NULL'): 'NOT NULL';
        $default   = isset($values['default'])? ( is_null($values['default'])? 'NULL': intval($values['default'])): 'NULL';
        $field     = sprintf("int(%s) %s default %s",$precision, $nullable ,$default );
        return $field;
    }
}