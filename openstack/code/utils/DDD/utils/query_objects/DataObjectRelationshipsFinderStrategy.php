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


/**
 * Class DataObjectRelationshipsFinderStrategy
 */
final class DataObjectRelationshipsFinderStrategy
{
    /**
     * @var QueryAlias
     */
    private $alias;
    /**
     * @var string
     */
    private $table_name;

    /**
     * DataObjectRelationshipsFinderStrategy constructor.
     * @param QueryAlias $alias
     * @param string $table_name
     */
    public function __construct(QueryAlias $alias, $table_name)
    {
        $this->alias      = $alias;
        $this->table_name = $table_name;
    }

    /**
     * @return array
     */
    public function find()
    {
        $base_entity   = singleton($this->table_name);
        $relation_name = $this->alias->getName();
        $class_name    = ClassInfo::baseDataClass($this->table_name);
        $subclasses    = array_keys(ClassInfo::subclassesFor($class_name));

        do {// relationships ...
            $has_many          = Config::inst()->get($class_name, 'has_many');
            $has_many_many     = Config::inst()->get($class_name, 'many_many');
            $has_one           = Config::inst()->get($class_name, 'has_one');
            $belongs_many_many = Config::inst()->get($class_name, 'belongs_many_many');

            if(!is_null($has_many) && array_key_exists($relation_name, $has_many)) break;
            if(!is_null($has_one)  && array_key_exists($relation_name, $has_one)) break;
            if(!is_null($has_many_many)  && array_key_exists($relation_name, $has_many_many)) break;
            if(!is_null($belongs_many_many)  && array_key_exists($relation_name, $belongs_many_many)) break;

            $class_name = array_pop($subclasses);

            if(is_null($class_name)) // we arrive to the end of the hierarchy and didnt find any relation with that name
                throw new InvalidArgumentException
                (
                    sprintf(' relation %s does not exist for %s', $relation_name, $this->table_name)
                );
            $base_entity = singleton($class_name);
        } while(true);

        return array($base_entity, $has_one, $has_many, $has_many_many, $belongs_many_many);
    }
}