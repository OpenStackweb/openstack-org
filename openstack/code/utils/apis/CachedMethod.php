<?php namespace Openstack\Annotations;

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
use Doctrine\Common\Annotations\Annotation;

/**
 * https://framework.zend.com/manual/1.10/en/zend.cache.theory.html
 * http://docs.doctrine-project.org/projects/doctrine-common/en/latest/reference/annotations.html
 * Class CachedMethod
 * @package Openstack\Annotations
 * @Annotation
 * @Target("METHOD")
 */
class CachedMethod extends Annotation
{
    /**
     * cache lifetime in seconds
     * @Required
     * @var int
     */
    public $lifetime;

    /**
     * @Enum({"RAW", "JSON"})
     * @var string
     */
    public $format = "JSON";

    /**
     * @var array<CacheMethodCondition>
     */
    public $conditions = [];
}