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
class SummitCategoryChange extends DataObject
{

	const STATUS_PENDING = 0;

	const STATUS_APPROVED = 1;

	const STATUS_REJECTED = 2;

    /**
     * @var array
     */
    private static $db = [
        'Comment' => 'Varchar',
        'ApprovalDate' => 'SS_DateTime',
        'Status' => 'Int',
    ];

    /**
     * @var array
     */
    private static $has_one = [
        'NewCategory' => 'PresentationCategory',
        'OldCategory' => 'PresentationCategory',
        'Presentation' => 'Presentation',
        'Reqester' => 'Member',
        'OldCatApprover' => 'Member',
        'NewCatApprover' => 'Member',
        'AdminApprover' => 'Member'
    ];

    public function getNiceStatus()
    {
    	switch($this->Status) {
    		case self::STATUS_PENDING:
    			return "Pending";
    		case self::STATUS_APPROVED:
    			return "Approved";
    		case self::STATUS_REJECTED:
    			return "Rejected";
    	}
    }

}