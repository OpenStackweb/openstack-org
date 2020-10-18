<?php
/**
 * Copyright 2017 Open Infrastructure Foundation
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
 * Class MandatoryDataObject
 * this extension is created to avoid using dummy fields on tables that
 * otherwise SS fwk would not create a table,
 * due that on API we use Doctrine ORM, is mandatory that all classes
 * on class hierarchy exists in order to have a proper mapping on doctrine
 */
class MandatoryDataObject extends DataExtension
{
    private $inserting = false;

    public function onBeforeWrite() {
        $id    = $this->owner->ID;
        if($id == 0) $this->inserting = true;
    }

    public function onAfterWrite() {
        if($this->inserting) {
            // insert ids over all hierarchy of tables without fields ( only ID)
            $id        = $this->owner->ID;
            $class     = get_class($this->owner);
            $singleton = singleton($class);
            foreach($singleton->getClassAncestry() as $anc_class) {
                $fields = DataObject::database_fields($anc_class);
                if(empty($fields))
                    DB::query("INSERT INTO {$anc_class} (ID) VALUES($id)");
            }
        }
    }

    public function onAfterDelete() {
        $id    = $this->owner->ID;
        $class = get_class($this->owner);
        $singleton = singleton($class);
        foreach($singleton->getClassAncestry() as $anc_class) {
            // remove ids over all hierarchy of tables without fields ( only ID)
            $fields = DataObject::database_fields($anc_class);
            if(empty($fields))
                DB::query("DELETE FROM {$anc_class} WHERE ID = {$id};");
        }
    }
}