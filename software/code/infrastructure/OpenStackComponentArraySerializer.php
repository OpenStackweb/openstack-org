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
final class OpenStackComponentArraySerializer implements IEntitySerializer
{

    /**
     * @param IEntity $entity
     * @return mixed
     */
    public function serialize(IEntity $entity)
    {
       if(!$entity instanceof OpenStackComponent) throw new InvalidArgumentException;

       $capability_tags = [];
       $grouped_capability_tags = [];
       foreach ($entity->CapabilityTags() as $tag) {
           $cat_name = $tag->Category()->Name;
           if (!array_key_exists($cat_name, $grouped_capability_tags)) $grouped_capability_tags[$cat_name] = [];
           $grouped_capability_tags[$cat_name][] = $tag->Name;
           $capability_tags[] = $tag->Name;
       }

       $res = array
       (
           'id'                         => $entity->ID,
           'name'                       => $entity->Name,
           'description'                => $entity->Description,
           'code_name'                  => $entity->CodeName,
           'slug'                       => $entity->getSlug(),
           'adoption'                   => $entity->Adoption,
           'age'                        => $entity->Age,
           'maturity_points'            => $entity->MaturityPoints,
           'grouped_capability_tags'    => $grouped_capability_tags,
           'capability_tags'            => $capability_tags
       );
       return $res;
    }
}