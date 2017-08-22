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

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\ClientException;

/**
 * Class FireBaseGCMApi
 * https://firebase.google.com/docs/cloud-messaging/http-server-ref
 * https://firebase.google.com/docs/cloud-messaging/send-message#send_messages_to_topics
 */
final class FireBaseGCMApi implements IPushNotificationApi
{

    const SendPushEndpoint   = 'https://fcm.googleapis.com/fcm/send';
    const TopicsEndpoint     = 'https://iid.googleapis.com/iid/v1/%s/rel/topics/%s';
    const Topics             = '/topics/';
    const BaseBackOff        =  2000;
    const AndroidRecipient   = 'android_%s';
    const IOSRecipient       = 'ios_%s';

    /**
     * @var string
     */
    private $api_server_key;

    /**
     * FireBaseGCMApi constructor.
     * @param string $api_server_key
     */
    public function __construct($api_server_key)
    {
        $this->api_server_key = $api_server_key;
    }

    /**
     * @param array $data
     * @param string $recipient
     * @param string $priority
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
    function sendPush($to, array $data, $priority = IPushNotificationMessage::NormalPriority, $platform = IPushNotificationMessage::PlatFormMobile, $ttl = null)
    {
        if ($platform == IPushNotificationMessage::PlatFormWeb)
            return   $this->sendPushWeb($to, $data, $priority, $ttl);
        return $this->sendPushMobile($to, $data, $priority, $ttl);
    }

    /**
     * @param Client $client
     * @param array $data
     * @param string $recipient
     * @param string $priority
     * @return ResponseInterface
     */
    private function sendRegularPayloadPush(Client $client, array $data, $recipient, $priority){
        $response = $client->post(self::SendPushEndpoint, [
            'headers' => [
                'Authorization' => sprintf('key=%s', $this->api_server_key),
            ],
            'json' => $this->createDataMessage($data, $recipient, $priority )
        ]);
        return $response;
    }

    /**
     * @param Client $client
     * @param array $data
     * @param string $recipient
     * @param string $priority
     * @return ResponseInterface
     */
    private function sendCustomPayloadPush(GuzzleHttp\Client $client, array $data, $recipient, $priority){
        $response = $client->post(self::SendPushEndpoint, [
            'headers' => [
                'Authorization' => sprintf('key=%s', $this->api_server_key),
            ],
            'json' => $this->createNotificationMessageWithCustomPayload($data, $recipient, $priority )
        ]);
        return $response;
    }
    /**
     * @param string $to
     * @param array $data
     * @param string $priority
     * @param null|int $ttl
     * @return bool
     */
    private function sendPushMobile($to, array $data, $priority = IPushNotificationMessage::NormalPriority, $ttl = null)
    {
        $client   = new Client();
        $res      = true;
        $attempt  = 1;

        try {
            foreach ($to as $recipient) {

                // create for droid ( data message)

                $response = $this->sendRegularPayloadPush($client, $data, sprintf(self::AndroidRecipient, $recipient), $priority );

                if ($response->getStatusCode() !== 200) $res = $res && false;

                usleep(self::BaseBackOff * $attempt);

                // create for ios ( notification message with custom payload)

                $response = $this->sendCustomPayloadPush($client, $data, sprintf(self::IOSRecipient, $recipient), $priority );
                if ($response->getStatusCode() !== 200) $res = $res && false;

                usleep(self::BaseBackOff * $attempt);

                // create for ios ( data message)

                $response = $this->sendRegularPayloadPush($client, $data, sprintf(self::IOSRecipient, $recipient), $priority );

                if ($response->getStatusCode() !== 200) $res = $res && false;

                usleep(self::BaseBackOff * $attempt);

                ++$attempt;
            }
            return $res;
        }
        catch (ClientException $ex1){
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
    private function sendPushWeb($to, array $data, $priority = IPushNotificationMessage::NormalPriority, $ttl = null)
    {
        $client   = new Client();
        $res      = true;
        $attempt  = 1;

        try {
            foreach ($to as $recipient) {

                $response = $this->sendRegularPayloadPush($client, $data, $recipient, $priority);

                if ($response->getStatusCode() !== 200) $res = $res && false;

                usleep(self::BaseBackOff * $attempt);

                ++$attempt;
            }
            return $res;
        }
        catch (ClientException $ex1){
            return false;
        }
    }


    /**
     * https://developers.google.com/instance-id/reference/server#create_relationship_maps_for_app_instances
     * Given a registration token and a supported relationship, you can create a mapping. For example, you can
     * subscribe an app instance to a Google Cloud Messaging topic by calling the Instance ID service at this endpoint,
     * providing the app instance's token as shown:
     * @param string $token
     * @param string $topic
     * @return bool
     */
    function subscribeToTopic($token, $topic){
        $endpoint = sprintf(self::TopicsEndpoint, $token, $topic);
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
        catch (ClientException $ex1){
            throw $ex1;
            return false;
        }
    }
}