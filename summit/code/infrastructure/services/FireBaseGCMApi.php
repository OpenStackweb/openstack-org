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

    const BaseUrl     = 'https://fcm.googleapis.com';
    const Topics      = '/topics/';
    const BaseBackOff = 2000;
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
    function sendPush($to, array $data, $priority = IPushNotificationApi::NormalPriority, $ttl = null)
    {
        $endpoint = self::BaseUrl.'/fcm/send';
        $client   = new GuzzleHttp\Client();
        $res      = true;
        $attempt  = 1;
        try {
            foreach ($to as $recipient) {
                // todo: this is a temporal solutions for IOS side, bc IOS is making the wrong assumption that has
                //       access to envelope, and its trying to parse the to field ( only available on envelope)
                //       so we send the to also on payload
                $data['to'] = self::Topics . $recipient;

                $message = [
                    'to'                => self::Topics . $recipient,
                    'data'              => $data,
                    'priority'          => $priority,
                    /*
                     * If you want to send messages consisting of only custom key-values to an iOS device when the app
                     * is in the background, set custom key-value pairs in the data key and set content_available to true.
                     * */
                    'content_available' => true,
                ];

                if(!is_null($ttl) && intval($ttl)  >= 0 ){
                    $message['time_to_live'] = intval($ttl);
                }

                $response = $client->post($endpoint, [
                    'headers' => [
                        'Authorization' => sprintf('key=%s', $this->api_server_key),
                        'Content-Type'  => 'application/json'
                    ],
                    'body' => json_encode($message)
                ]);

                if ($response->getStatusCode() !== 200) $res = $res && false;

                usleep(self::BaseBackOff * $attempt);
                ++$attempt;
            }
            return $res;
        }
        catch (\GuzzleHttp\Exception\ClientException $ex1){
            return false;
        }
    }
}