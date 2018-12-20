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
class PushNotificationMessage extends DataObject implements IPushNotificationMessage
{
    use CustomDataObject;

    private static $db = array
    (
        'Message'   => 'Text',
        'Approved'  => 'Boolean',
        'IsSent'    => 'Boolean',
        'SentDate'  => 'SS_Datetime',
        'Priority'  => "Enum('NORMAL, HIGH', 'NORMAL')",
        'Platform'  => "Enum('MOBILE, WEB','MOBILE')"
    );

    private static $has_one = array
    (
        'Owner'      => 'Member',
        'ApprovedBy' => 'Member',
    );

    /**
     * @return int
     */
    public function getIdentifier()
    {
        return intval($this->getField('ID'));
    }

    public function sent()
    {
        if($this->isAlreadySent()) throw new EntityValidationException('Push notification already sent!.');
        $this->IsSent   = true;
        $this->SentDate = CustomMySQLDatabase::nowRfc2822();
    }

    public function approve(){
        $this->Approved     = true;
        $this->ApprovedByID = Member::currentUserID();
    }

    /**
     * @return boolean
     */
    public function isAlreadySent(){
        return $this->IsSent;
    }

    protected function onBeforeWrite() {
        parent::onBeforeWrite();
        if($this->getIdentifier() === 0)
            $this->OwnerID = Member::currentUserID();
    }

    /**
     * @return int
     */
    public function getTimestamp(){
        // force from local time zone ( web server) to UTC
        $web_server_time_zone = Config::inst()->get('MySQLDatabase', 'web_server_time_zone');
        if(empty($web_server_time_zone))
            $web_server_time_zone = CustomMySQLDatabase::DefaultWebServerTimeZone;

        $utc_timezone = new DateTimeZone("UTC");
        $time_zone   = new \DateTimeZone($web_server_time_zone);
        $date        = new \DateTime($this->getField('Created'), $time_zone);
        $date->setTimezone($utc_timezone);

        return $date->getTimestamp();
    }

    const PushType = 'PUSH_NOTIFICATION';

    /**
     * @return string
     */
    public function getType(){
        return self::PushType;
    }

    /**
     * @return string
     */
    public function getPlatform()
    {
        return $this->getField('Platform');
    }

    /**
     * @return string
     */
    public function getPriority()
    {
        return $this->getField('Priority');
    }
}