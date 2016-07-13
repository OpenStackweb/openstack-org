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
 * Class BelongsManyManyJoinStrategy
 */
final class BelongsManyManyJoinStrategy extends AbstractJoinStrategy
{

    /**
     * @return JoinSpecification[]
     */
    public function build()
    {
        $specs = array();

        $class_name              = ClassInfo::baseDataClass($this->base_entity);
        $relation_name           = $this->alias->getName();
        $child                   = $this->relations[$relation_name];
        $child_many_many         = Config::inst()->get($child, 'many_many');
        $child_many_many_classes = array_flip($child_many_many);
        $component_name = $child_many_many_classes[$class_name];
        list($parent_class, $component_class, $child_join_field, $join_field, $join_table) = Singleton($child)->many_many($component_name);

        $specs[] = new JoinSpecification
        (
            $join_table,
            $join_table . '`.' . $join_field . ' = `' . $class_name . '`.ID'

        );

        $specs[] = new JoinSpecification
        (
            $child,
            $child . '`.ID = `' . $join_table . '`.' . $child_join_field

        );
        $this->base_table = $class_name;
        return $specs;
    }
}