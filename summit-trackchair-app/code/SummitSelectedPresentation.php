<?php

/**
 * Copyright 2014 Openstack Foundation
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
class SummitSelectedPresentation extends DataObject
{

	const COLLECTION_SELECTED = 'selected';	

	const COLLECTION_MAYBE = 'maybe';

	const COLLECTION_PASS = 'pass';

    /**
     * @var array
     */
    private static $db = [
        'Order' => 'Int',
        'Collection' => "Enum('maybe, selected, pass')"
    ];

    /**
     * @var array
     */
    private static $has_one = [
        'SummitSelectedPresentationList' => 'SummitSelectedPresentationList',
        'Presentation' => 'Presentation',
        'Member' => 'Member'
    ];

    public function isSelected()
    {
    	return $this->Collection === self::COLLECTION_SELECTED;
    }

    public function isMaybe()
    {
    	return $this->Collection === self::COLLECTION_MAYBE;
    }

    public function isPass()
    {
    	return $this->Collection === self::COLLECTION_PASS;
    }

}