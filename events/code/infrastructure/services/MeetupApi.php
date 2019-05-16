<?php
/**
 * Copyright 2019 OpenStack Foundation
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
/**
 * Class MeetupApi
 */
final class MeetupApi implements IExternalEventsApi
{

    const BaseUrl = 'https://api.meetup.com';

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var Client
     */
    private $client;

    /**
     * @param string $apiKey
     */
    public function setApiKey(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * @param int $pageSize
     * @return array
     */
    public function getAllUpcomingEvents(int $pageSize): array
    {
        $res = [];
        try {

            $groups = $this->getGroups($pageSize);
            foreach ($groups as $group) {
                $upcomingEvents = $this->getGroupIncomingEvents($group['urlname'], $pageSize);
                $res = array_merge($res, $upcomingEvents);
            }
        }
        catch (Exception $ex){
            SS_Log::log($ex->getMessage(), SS_Log::WARN);
        }
        return $res;
    }

    /**
     * @param int $page
     * @return array
     * @throws Exception
     */
    public function getGroups(int $page = 20):array{

        $query = [
            'page' => $page,
            'key' => $this->apiKey
        ];
        $api_url = sprintf("%s/self/groups", self::BaseUrl);
        $response = $this->client->get($api_url, array
            (
                'query' => $query
            )
        );

        if($response->getStatusCode() !== 200)
            throw new Exception('invalid status code!');
        $content_type = $response->getHeaderLine('content-type');
        if(empty($content_type))
            throw new Exception('invalid content type!');
        if(!strstr($content_type,'application/json'))
            throw new Exception('invalid content type!');

        $json = $response->getBody()->getContents();
        return json_decode($json, true);
    }


    /**
     * @param string $groupSlug
     * @param int $page
     * @return array
     * @throws Exception
     */
    public function getGroupIncomingEvents(string $groupSlug, int $page = 20):array {

        $query = [
            'page' => $page,
            'key' => $this->apiKey,
            'status' => 'upcoming'
        ];
        $api_url = sprintf("%s/%s/events", self::BaseUrl, $groupSlug);
        $response = $this->client->get($api_url, array
            (
                'query' => $query
            )
        );

        if($response->getStatusCode() !== 200)
            throw new Exception('invalid status code!');
        $content_type = $response->getHeaderLine('content-type');
        if(empty($content_type))
            throw new Exception('invalid content type!');
        if(!strstr($content_type,'application/json'))
            throw new Exception('invalid content type!');
        $json = $response->getBody()->getContents();
        return json_decode($json, true);
    }
}