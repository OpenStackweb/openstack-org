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
 * Class HasManyManyJoinStrategy
 */
final class HasManyManyJoinStrategy extends AbstractJoinStrategy
{

    /**
     * @return JoinSpecification[]
     */
    public function build()
    {
        $specs              = array();
        $base_entity_name   = get_class($this->base_entity);
        $relation_name      = $this->alias->getName();
        $class_name         = ClassInfo::baseDataClass($this->base_entity);
        $join_table         = "{$base_entity_name}_{$relation_name}";
        $child_class        = $this->relations[$relation_name];
        $child_hierarchy    = ClassInfo::dataClassesFor($child_class);
        $base_child_class   = array_shift($child_hierarchy);
        $parent_field       = $base_entity_name . "ID";
        $child_field        = $child_class . "ID";

        $specs[] = new JoinSpecification
        (
            $join_table,
            $join_table . '.' . $parent_field . ' = ' . $class_name . '.ID'
        );

        $specs[] = new JoinSpecification
        (
            $child_class,
            $child_class . '.ID = ' . $join_table . '.' . $child_field
        );

        $this->base_table = $base_child_class;

        return $specs;
    }
}