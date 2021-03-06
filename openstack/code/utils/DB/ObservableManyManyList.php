<?php

/**
 * Copyright 2015 Open Infrastructure Foundation
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
 * Class ObservableManyManyList
 */
class ObservableManyManyList extends ManyManyList
{

    public function __construct($dataClass, $joinTable, $localKey, $foreignKey, $extraFields = array())
    {
        parent::__construct($dataClass, $joinTable, $localKey, $foreignKey, $extraFields );
    }

    public function add($item, $extraFields = null)
    {
        parent::add($item, $extraFields);
        PublisherSubscriberManager::getInstance()->publish('manymanylist_added_item', array($this, $item));
    }

    public function remove($item)
    {
        parent::remove($item);
        PublisherSubscriberManager::getInstance()->publish('manymanylist_removed_item', array($this, $item));
    }
}