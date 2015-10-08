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
final class SoftwareManager implements ISoftwareManager
{

    /**
     * @var IEntitySerializer
     */
    private $serializer;

    /**
     * @param IEntitySerializer $serializer
     */
    public function __construct(IEntitySerializer $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param IOpenStackRelease $release
     * @param string $term
     * @param int $adoption
     * @param int $maturity
     * @param int $age
     * @return array
     */
    public function getComponents(IOpenStackRelease $release , $term = '', $adoption = 0, $maturity = 0, $age = 0)
    {
        $res1 = array();
        $res2 = array();

        $core_components     = $release->getOpenStackCoreComponents($term, $adoption, $maturity, $age);
        $optional_components = $release->getOpenStackOptionalComponents($term, $adoption, $maturity, $age);

        foreach($core_components as $c)
        {
            array_push($res1, $this->serializer->serialize($c));
        }

        foreach($optional_components as $c)
        {
            array_push($res2, $this->serializer->serialize($c));
        }

        return array($res1, $res2);
    }
}