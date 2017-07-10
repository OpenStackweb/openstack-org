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
final class PresentationChangeRequestFireBasePushNotificationSerializationStrategy implements IFireBasePushNotificationSerializationStrategy
{
    /**
     * @var PresentationChangeRequestPushNotification
     */
    private $notification;

    /**
     * FireBasePushNotificationSerializationStrategy constructor.
     * @param PresentationChangeRequestPushNotification $notification
     */
    public function __construct(PresentationChangeRequestPushNotification $notification)
    {
        $this->notification = $notification;
    }

    /**
     * @return array
     */
    function getToField()
    {
        $to = null;

        switch($this->notification->Channel)
        {
            case 'TRACKCHAIRS':
            {
                $to = ['trackchairs'];
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
            'id'            => intval($this->notification->ID),
            'type'          => $this->notification->getType(),
            'body'          => $this->notification->Message,
            'presentation'  => $this->serializePresentation(),
            'channel'       => $this->notification->Channel,
            'created_at'    => $this->notification->getTimestamp(),
        ];

        return $data;
    }

    /**
     * returns presentation object as json for firebase ingestion
     * @return string
     */
    private function serializePresentation()
    {
        $presentation_array = array();
        $presentation_array['id']                    = $this->notification->Presentation()->ID;
        $presentation_array['change_requests_count'] = $this->notification->Presentation()->ChangeRequests()->filter('Status', SummitCategoryChange::STATUS_PENDING)->Count();
        $presentation_array['comment_count']         = $this->notification->Presentation()->Comments()->Count();

        return json_encode($presentation_array);
    }
}