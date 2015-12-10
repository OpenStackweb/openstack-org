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
class CustomUnsavedRelationList extends UnsavedRelationList
{
    public function __construct($baseClass, $relationName, $dataClass)
    {
        parent::__construct($baseClass, $relationName, $dataClass);
        $ids = Session::get($this->itemsSessionKey());
        if(is_null($ids)) return;
        foreach($ids as $id)
        {
            $this->add($dataClass::get()->byID($id));
        }
    }

    public static function clearSessionData($baseClass, $relationName, $dataClass)
    {
        $key = sprintf("%s-%s-%s-%s", Member::currentUserID(), $baseClass, $relationName, $dataClass);
        Session::clear($key);
    }

    private function itemsSessionKey()
    {
        return sprintf("%s-%s-%s-%s", Member::currentUserID(), $this->baseClass, $this->relationName, $this->dataClass);
    }

    /**
     * Add an item to this relationship
     *
     * @param $extraFields A map of additional columns to insert into the joinTable in the case of a many_many relation
     */
    public function add($item, $extraFields = null) {
        $ids = Session::get($this->itemsSessionKey());
        if(is_null($ids)) $ids = array();
        $ids[$item->ID] = $item->ID;
        Session::set($this->itemsSessionKey(), $ids);
        parent::add($item, $extraFields);
    }

    public function remove($item) {
        parent::remove($item);
        $ids = Session::get($this->itemsSessionKey());
        if(is_null($ids)) return;
        unset($ids[$item->ID]);
        Session::set($this->itemsSessionKey(), $ids);
    }

    public function removeAll(){
        parent:;$this->removeAll();
        Session::clear($this->itemsSessionKey());
    }

    public function byID($id) {
        $ids = Session::get($this->itemsSessionKey());
        if(is_null($ids)) return null;
        if(!isset($ids[$id])) return null;
        $dataClass = $this->dataClass;
        return $dataClass::get()->byID($id);
    }
}