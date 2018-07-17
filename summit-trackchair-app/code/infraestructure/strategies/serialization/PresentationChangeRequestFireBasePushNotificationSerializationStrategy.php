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
        $presentation_array = [];
        $presentation_id = intval($this->notification->PresentationID);
        $presentation = Presentation::get()->byID($presentation_id);
        if(is_null($presentation))
            throw new InvalidArgumentException("Presentation does not exists!");
        $presentation_array['id']                    = $presentation_id;
        $presentation_array['change_requests_count'] = $presentation->ChangeRequests()->filter('Status', SummitCategoryChange::STATUS_PENDING)->Count();
        $presentation_array['comment_count']         = $presentation->Comments()->Count();

        return json_encode($presentation_array);
    }
}