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
class EventbriteEvent extends DataObject implements IEventbriteEvent
{
    private static $db = array
    (
        'ExternalOrderId'     => "Enum('ORDER_PLACED,EVENT_ADDED, EVENT_UPDATE, NONE','NONE')",
        'ApiUrl'        => 'Varchar(512)',
        'Processed'     => 'Boolean',
        'ProcessedDate' => 'SS_DateTime',
        'FinalStatus'   => 'Varchar(255)',
    );

    private static $has_one = array
    (
        'Summit' => 'Summit',
    );

    private static $has_many = array
    (
        'Attendees' => 'EventbriteAttendee'
    );

    static $indexes = array
    (

    );

    /**
     * @param string $status
     * @throws Exception
     */
    public function markAsProcessed($status)
    {
        if($this->Processed) throw new Exception('EventbriteEvent already processed!');

        $this->Processed     = true;
        $this->ProcessedDate = MySQLDatabase56::nowRfc2822();
        $this->FinalStatus   = $status;
    }

    /**
     * @return int
     */
    public function getIdentifier()
    {
        return (int)$this->getField('ID');
    }

    /**
     * @return string
     */
    public function getApiUrl()
    {
        return $this->getField('ApiUrl');
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->getField('EventType');
    }

    /**
     * @return bool
     */
    public function isAlreadyProcessed()
    {
        return $this->Processed;
    }
}