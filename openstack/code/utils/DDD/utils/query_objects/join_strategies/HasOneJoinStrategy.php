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
 * Class HasOneJoinStrategy
 */
class HasOneJoinStrategy extends AbstractJoinStrategy
{

    /**
     * @return JoinSpecification[]
     */
    public function build()
    {
        $specs                   = array();
        $relation_name           = $this->alias->getName();
        $child_class             = $this->relations[$relation_name];
        $child_hierarchy         = ClassInfo::dataClassesFor($child_class);
        $base_child_class        = array_shift($child_hierarchy);
        $class_name              = ClassInfo::baseDataClass($this->base_entity);
        $specs[] = new JoinSpecification
        (
            $child_class,
            $child_class . '.ID = ' . $class_name . '.' . $relation_name . 'ID'
        );
        $this->base_table = $base_child_class;
        return $specs;
    }
}