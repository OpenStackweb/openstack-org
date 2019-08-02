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
        $this->defaultVal = null ;
    }

    public function nullValue() {
        return null;
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

    public function scaffoldFormField($title = null, $params = null) {
        $relationName = substr($this->name,0,-2);
        $hasOneClass = $this->object->hasOneComponent($relationName);

        if($hasOneClass && singleton($hasOneClass) instanceof Image) {
            $field = UploadField::create($relationName, $title);
            $field->getValidator()->setAllowedExtensions(array('jpg', 'jpeg', 'png', 'gif'));
        } elseif($hasOneClass && singleton($hasOneClass) instanceof File) {
            $field = UploadField::create($relationName, $title);
        } else {
            $titleField = (singleton($hasOneClass)->hasField('Title')) ? "Title" : "Name";
            $list = DataList::create($hasOneClass);
            // Don't scaffold a dropdown for large tables, as making the list concrete
            // might exceed the available PHP memory in creating too many DataObject instances
            if($list->count() < 100) {
                $field = DropdownField::create($this->name, $title, $list->map('ID', $titleField));
                $field->setEmptyString(' ');
            } else {
                $field = NumericField::create($this->name, $title);
            }

        }

        return $field;
    }
}