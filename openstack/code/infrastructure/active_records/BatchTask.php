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

/**
 * Class BatchTask
 */
final class BatchTask
    extends DataObject
    implements IBatchTask
{

    static $create_table_options = array('MySQLDatabase' => 'ENGINE=InnoDB');

    static $db = array(
        'Name'             => 'Text',
        'LastResponse'     => 'Text',
        'LastRecordIndex'  => 'Int',
        'LastResponseDate' => 'SS_Datetime',
        'TotalRecords'     => 'Int',
        'CurrentPage'      => 'Int',
    );

    /***
     * @return string
     */
    public function name()
    {
        return (string)$this->getField('Name');
    }

    /***
     * @return int
     */
    public function lastRecordProcessed()
    {
        return (int)$this->getField('LastRecordIndex');
    }

    /**
     * @return string
     */
    public function lastResponse()
    {
        return (string)$this->getField('LastResponse');
    }

    /**
     * @return DateTime
     */
    public function lastResponseDate()
    {
        return new DateTime($this->getField('LastResponseDate'));
    }

    /**
     * @return int
     */
    public function totalRecords()
    {
        return (int)$this->getField('TotalRecords');
    }

    /**
     * @param string $response
     * @return void
     */
    public function updateResponse($response)
    {
        $this->setField('LastResponse', $response);
    }

    /**
     * @return void
     */
    public function updateLastRecord()
    {
        $last = $this->lastRecordProcessed();
        $this->setField('LastRecordIndex', $last + 1);
    }

    /**
     * @return int
     */
    public function getIdentifier()
    {
        return (int)$this->getField('ID');
    }

    /**
     * @param int $total_qty
     * @param int $current_page
     * @return void
     */
    public function initialize($total_qty, $current_page = 0)
    {
        $this->setField('LastRecordIndex', 0);
        $this->setField('TotalRecords', $total_qty);
        $this->setField('CurrentPage', $current_page);
    }

    /**
     * @return int
     */
    public function getCurrentPage()
    {
        return (int)$this->getField('CurrentPage');
    }

    /**
     * @param int $count
     * @param int $page_size
     * @return $this
     */
    public function updatePage($count, $page_size)
    {
        $last     = $this->getCurrentPage();
        $max_page = (int)ceil(($count/$page_size) + 1);
        $current  = $last + 1;
        $this->setField('TotalRecords', $count);
        if($current > $max_page) $current = 1;
        $this->setField('CurrentPage', $current);
        return $this;
    }
}