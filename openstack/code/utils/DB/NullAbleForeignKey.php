<?php
/**
 * Copyright 2016 OpenStack Foundation
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

class NullAbleForeignKey extends ForeignKey
{
    public function __construct($name, $object = null) {
        parent::__construct($name, $object);
        $this->defaultVal = 0 ;
    }

    public function nullValue() {
        return "0";
    }

    public function requireField() {

        $parts =
        [
            'datatype'   => 'int',
            'precision'  => 11,
            'null'       => 'null',
            'default'    => $this->defaultVal,
            'arrayValue' => $this->arrayValue
        ];

        $values= ['type'=>'int', 'parts'=> $parts];
        DB::requireField($this->tableName, $this->name, $values);
    }
}