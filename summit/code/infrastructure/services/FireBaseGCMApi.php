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

    const BaseUrl            = 'https://fcm.googleapis.com';
    const Topics             = '/topics/';
    const BaseBackOff        = 2000;
    /**
     * @var string
     */
    private $api_server_key;

    public function __construct($api_server_key)
    {
        $this->api_server_key = $api_server_key;
    }

    /**
     * @param array $data
     * @param $recipient
     * @param $priority
     * @return array
     */
    private function createDataMessage(array $data, $recipient, $priority ){

        return [
            'to'                => self::Topics . $recipient,
            'data'              => $data,
            'priority'          => $priority,
        ];
    }

    /**
     * @param array $data
     * @param $recipient
     * @param $priority
     * @return array
     */
    private function createNotificationMessageWithCustomPayload(array $data, $recipient, $priority )
    {

        $to                   = self::Topics . $recipient;
        $data['to']           = $to;
        $notification         = [];
        $notification['body'] = $data['body'];

        if (isset($data['title']))
            $notification['title'] = $data['title'];

        return [
            'to'           => $to,
            'data'         => $data,
            'priority'     => $priority,
            'notification' => $notification,
        ];

    }

    /**
     * @param string $to
     * @param array $data
     * @param string $priority
     * @param string $platform
     * @param null|int $ttl
     * @return bool
     */
    function sendPush($to, array $data, $priority = IPushNotificationApi::NormalPriority, $platform = 'MOBILE', $ttl = null)
    {
        $res      = true;

        if ($platform == 'WEB') {
            $res = $this->sendPushWeb($to, $data, $priority, $ttl);
        } else {
            $res = $this->sendPushMobile($to, $data, $priority, $ttl);
        }

        return $res;
    }

    /**
     * @param string $to
     * @param array $data
     * @param string $priority
     * @param null|int $ttl
     * @return bool
     */
    function sendPushMobile($to, array $data, $priority = IPushNotificationApi::NormalPriority, $ttl = null)
    {
        $endpoint = self::BaseUrl.'/fcm/send';
        $client   = new GuzzleHttp\Client();
        $res      = true;
        $attempt  = 1;

        try {
            foreach ($to as $recipient) {

                // create for droid ( data message)

                $response = $client->post($endpoint, [
                    'headers' => [
                        'Authorization' => sprintf('key=%s', $this->api_server_key),
                        'Content-Type'  => 'application/json'
                    ],
                    'body' => json_encode($this->createDataMessage($data, 'android_'.$recipient, $priority ))
                ]);

                if ($response->getStatusCode() !== 200) $res = $res && false;

                usleep(self::BaseBackOff * $attempt);

                // create for ios ( notification message with custom payload)

                $response = $client->post($endpoint, [
                    'headers' => [
                        'Authorization' => sprintf('key=%s', $this->api_server_key),
                        'Content-Type'  => 'application/json'
                    ],
                    'body' => json_encode($this->createNotificationMessageWithCustomPayload($data, 'ios_'.$recipient, $priority ))
                ]);

                if ($response->getStatusCode() !== 200) $res = $res && false;

                usleep(self::BaseBackOff * $attempt);

                // create for ios ( data message)

                $response = $client->post($endpoint, [
                    'headers' => [
                        'Authorization' => sprintf('key=%s', $this->api_server_key),
                        'Content-Type'  => 'application/json'
                    ],
                    'body' => json_encode($this->createDataMessage($data, 'ios_'.$recipient, $priority ))
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

    /**
     * @param string $to
     * @param array $data
     * @param string $priority
     * @param null|int $ttl
     * @return bool
     */
    function sendPushWeb($to, array $data, $priority = IPushNotificationApi::NormalPriority, $ttl = null)
    {
        $endpoint = self::BaseUrl.'/fcm/send';
        $client   = new GuzzleHttp\Client();
        $res      = true;
        $attempt  = 1;

        try {
            foreach ($to as $recipient) {

                // create for web ( data message)
                $data = $this->createDataMessage($data, $recipient, $priority );
                //$data['content_available'] = false;

                $response = $client->post($endpoint, [
                    'headers' => [
                        'Authorization' => sprintf('key=%s', $this->api_server_key),
                        'Content-Type'  => 'application/json'
                    ],
                    'body' => json_encode($data)
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


    /**
     * @param string $token
     * @param string $topic
     * @return bool
     */
    function subscribeToTopicWeb($token, $topic){
        $endpoint = 'https://iid.googleapis.com/iid/v1/'.$token.'/rel/topics/'.$topic;
        $client   = new GuzzleHttp\Client();
        $res      = true;

        try {
            $response = $client->post($endpoint, [
                'headers' => [
                    'Authorization' => sprintf('key=%s', $this->api_server_key)
                ]
            ]);

            if ($response->getStatusCode() !== 200) $res = $res && false;

            return $res;
        }
        catch (\GuzzleHttp\Exception\ClientException $ex1){
            throw $ex1;
            return false;
        }

    }
}