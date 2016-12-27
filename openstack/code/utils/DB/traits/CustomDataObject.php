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
 * Class CustomDataObject
 */
trait CustomDataObject
{
    public function getManyManyComponents($componentName, $filter = "", $sort = "", $join = "", $limit = "") {
        list($parentClass, $componentClass, $parentField, $componentField, $table) = $this->many_many($componentName);

        // If we haven't been written yet, we can't save these relations, so use a list that handles this case
        if(!$this->ID) {
            if(!isset($this->unsavedRelations[$componentName])) {
                $this->unsavedRelations[$componentName] =
                    UnsavedRelationList::create($parentClass, $componentName, $componentClass);
            }
            return $this->unsavedRelations[$componentName];
        }

        $result = ManyManyList::create($componentClass, $table, $componentField, $parentField,
            $this->many_many_extraFields($componentName));
        if($this->model) $result->setDataModel($this->model);

        // If this is called on a singleton, then we return an 'orphaned relation' that can have the
        // foreignID set elsewhere.
        $result = $result->forForeignID($this->ID);

        return $result->where($filter)->sort($sort)->limit($limit);
    }

    public function getComponents($componentName, $filter = "", $sort = "", $join = "", $limit = null) {
        $result = null;

        if(!$componentClass = $this->has_many($componentName)) {
            user_error("DataObject::getComponents(): Unknown 1-to-many component '$componentName'"
                . " on class '$this->class'", E_USER_ERROR);
        }

        if($join) {
            throw new \InvalidArgumentException(
                'The $join argument has been removed. Use leftJoin($table, $joinClause) instead.'
            );
        }

        // If we haven't been written yet, we can't save these relations, so use a list that handles this case
        if(!$this->ID) {
            if(!isset($this->unsavedRelations[$componentName])) {
                $this->unsavedRelations[$componentName] =
                    UnsavedRelationList::create($this->class, $componentName, $componentClass);
            }
            return $this->unsavedRelations[$componentName];
        }

        $joinField = $this->getRemoteJoinField($componentName, 'has_many');

        $result = HasManyList::create($componentClass, $joinField);
        if($this->model) $result->setDataModel($this->model);
        $result = $result->forForeignID($this->ID);

        $result = $result->where($filter)->limit($limit)->sort($sort);

        return $result;
    }
}