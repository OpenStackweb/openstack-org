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
final class PresentationPushNotification extends PushNotificationMessage
{
    const PushType = 'PUSH_NOTIFICATION';

    private static $db = array
    (
        'Channel'  => "Enum('TRACKCHAIRS', 'TRACKCHAIRS')",
    );

    private static $summary_fields = array
    (
    );

    private static $has_one = array
    (
        'Presentation' => 'Presentation',
    );

    private static $many_many = array
    (
    );

    private static $indexes = array(

    );

    protected function onBeforeWrite()
    {
        parent::onBeforeWrite();
        $this->Platform = 'WEB';
        $this->approve();
    }

    /**
     * returns data for firebase ingestion
     * @return array
     */
    public function getDataForFCM()
    {
        $data  = [
            'id'            => intval($this->ID),
            'type'          => PresentationPushNotification::PushType,
            'body'          => $this->Message,
            'presentation'  => $this->presentationToJson(),
            'channel'       => $this->Channel,
            'created_at'    => $this->getTimestamp(),
        ];

        return $data;
    }

    /**
     * returns recipient for firebase ingestion
     * @return array
     */
    public function getRecipientForFCM()
    {
        $to = null;

        switch($this->Channel)
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
     * returns presentation object as json for firebase ingestion
     * @return string
     */
    public function presentationToJson()
    {
        $presentation_array = array();
        $presentation_array['id'] = $this->Presentation()->ID;
        $presentation_array['change_request_count'] = $this->Presentation()->ChangeRequests()->Count();
        $presentation_array['comments'] = array();

        foreach ($this->Presentation()->Comments() as $c ) {
            $comment = array();
            $comment['body'] = $c->Body;
            $comment['name'] = $c->Commenter()->getName();
            $comment['ago'] =  $c->obj('Created')->Ago(false);
            $comment['is_activity'] = (boolean) $c->IsActivity;
            $presentation_array['comments'][] = $comment;
        }

        return json_encode($presentation_array);
    }

}