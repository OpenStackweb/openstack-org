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
class TagManagerField extends FormField
{
    public function FieldHolder($attributes = array ())
    {
        Requirements::javascript('themes/openstack/bower_assets/jquery-validate/dist/jquery.validate.min.js');
        Requirements::javascript("summit/javascript/forms/tagmanagerfield/tagmanagerfield.bundle.js");
        return parent::FieldHolder($attributes);
    }

    public function saveInto(DataObjectInterface $record) {
        if($this->name) {
            $tags = explode(',',$this->dataValue());
            if(!$record instanceof SummitEvent) return;
            $record->Tags()->removeAll();
            foreach($tags as $t)
            {
                $tag = Tag::get()->filter('Tag', $t)->first();
                if(is_null($tag))
                {
                    $tag = Tag::create(array('Tag' => $t));
                    $tag->write();
                }
                $record->Tags()->add($tag);
            }
        }
    }

    public function setValue($value) {
        if($value instanceof ManyManyList) {
            $tags = $value->toArray();
            $list = array();
            foreach ($tags as $t) {
                array_push($list, $t->Tag);
            }
            $this->value = implode(',', $list);
        }
        if(is_string($value))
        {
            $this->value = $value;
        }
        return $this;
    }
}