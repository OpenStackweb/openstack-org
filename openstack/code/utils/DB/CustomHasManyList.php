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
class CustomHasManyList extends HasManyList
{
    public function __construct($dataClass, $foreignKey) {
        parent::__construct($dataClass, $foreignKey);
    }

    protected function foreignIDFilter($id = null) {
        if ($id === null) $id = $this->getForeignID();
        $class      = $this->dataClass;
        $class_type = $class::create();
        $has_field  =  !is_null( $class_type->hasOwnTableDatabaseField($this->foreignKey));
        if(!$has_field)
        {
            foreach(ClassInfo::ancestry($class_type) as $parent_class)
            {
                if(in_array($parent_class, array('ViewableData', 'Object', 'DataObject'))) continue;
                $class_type_parent = $parent_class::create();
                $has_field  =  !is_null( $class_type_parent->hasOwnTableDatabaseField($this->foreignKey));
                if($has_field){
                    $class = $parent_class;
                    break;
                }
            }
        }

        // Apply relation filter
        if(is_array($id)) {
            return "\"$class\".\"$this->foreignKey\" IN ('" .
            implode("', '", array_map('Convert::raw2sql', $id)) . "')";
        } else if($id !== null){
            return "\"$class\".\"$this->foreignKey\" = '" .
            Convert::raw2sql($id) . "'";
        }
    }
}