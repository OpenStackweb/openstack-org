<?php

/**
 * Copyright 2017 OpenStack Foundation
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
final class SummitFireBasePushNotificationSerializationStrategy
    implements IFireBasePushNotificationSerializationStrategy
{

    /**
     * @var SummitPushNotification
     */
    private $notification;

    /**
     * FireBasePushNotificationSerializationStrategy constructor.
     * @param SummitPushNotification $notification
     */
    public function __construct(SummitPushNotification $notification)
    {
        $this->notification = $notification;
    }

    /**
     * @return array
     */
    function getToField()
    {
        // Push to speakers
        $to = null;

        switch($this->notification->Channel)
        {
            case 'SPEAKERS':
            {
                $to = ['speakers'];
            }
                break;
            case 'ATTENDEES':
            {
                $to = ['attendees'];
            }
                break;
            case 'MEMBERS':
            {
                $recipients = array();

                foreach($this->notification->Recipients() as $m)
                {
                    array_push($recipients, 'member_'.$m->ID);
                }
                $to = $recipients;
            }
                break;
            case 'SUMMIT':
            {
                $to = ['summit_'.$this->notification->SummitID];
            }
                break;
            case 'EVENT':
            {
                $to   = ['event_'.$this->notification->EventID];
            }
                break;
            case 'EVERYONE':
            {
                $to = ['everyone'];
            }
                break;
            case 'GROUP':
            {
                $recipients = array();
                foreach($this->notification->Group()->Members() as $m)
                {
                    array_push($recipients, 'member_'.$m->ID);
                }
                $to = $recipients;
            }
                break;
        }

        return $to;
    }

    /**
     * @return array
     */
    function getDataField()
    {
        $data  = [
            'id'         => intval($this->notification->ID),
            'type'       => $this->notification->getType(),
            'body'       => $this->notification->Message,
            'summit_id'  => intval($this->notification->SummitID),
            'channel'    => $this->notification->Channel,
            'created_at' => $this->notification->getTimestamp(),
        ];

        if($this->notification->Channel == 'EVENT')
        {
            $extra_data     = [
                'event_id' => $this->notification->EventID,
                'title'    => $this->notification->Event()->getTitle(),
            ];

            $data = array_merge($data, $extra_data);
        }

        return $data;
    }
}