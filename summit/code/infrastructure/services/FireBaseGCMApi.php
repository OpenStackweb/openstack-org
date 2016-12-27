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

/**
 * Class FireBaseGCMApi
 * https://firebase.google.com/docs/cloud-messaging/http-server-ref
 * https://firebase.google.com/docs/cloud-messaging/send-message#send_messages_to_topics
 */
final class FireBaseGCMApi implements IPushNotificationApi
{

    const BaseUrl = 'https://fcm.googleapis.com';
    /**
     * @var string
     */
    private $api_server_key;

    public function __construct($api_server_key)
    {
        $this->api_server_key = $api_server_key;
    }

    /**
     * @param string $to
     * @param array $data
     * @param string $priority
     * @param null|int $ttl
     * @return bool
     */
    function sendPush($to, array $data, $priority = self::NormalPriority, $ttl = null)
    {
        $endpoint = self::BaseUrl.'/fcm/send';
        $client   = new GuzzleHttp\Client();
        $res      = true;
        try {
            foreach ($to as $recipient) {

                $message = [
                    'to' => '/topics/' . $recipient,
                    'data' => $data
                ];

                $response = $client->post($endpoint, [
                    'headers' => [
                        'Authorization' => sprintf('key=%s', $this->api_server_key),
                        'Content-Type' => 'application/json'
                    ],
                    'body' => json_encode($message)
                ]);

                if ($response->getStatusCode() !== 200) $res = $res && false;
            }
            return $res;
        }
        catch (\GuzzleHttp\Exception\ClientException $ex1){
            return false;
        }
    }
}