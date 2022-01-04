<?php
/**
 * Copyright 2019 Open Infrastructure Foundation
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
    const BaseAuthUrl = 'https://secure.meetup.com';
    const Throttle = 1000; // ms
    /**
     * @var Client
     */
    private $client;
    /**
     * @param string $key
     * @return CacheProxy|Zend_Cache_Core
     */
    private function getCache($key = 'all')
    {
        return SS_Cache::factory(strtolower(get_class($this)) . '_MeetupApi_'.strtolower($key));
    }
    /**
     * @param $key
     * @return mixed|null
     */
    private function loadRAWFromCache($key)
    {
        if ($result = $this->getCache()->load(md5($key))) {
            return unserialize($result);
        }
        return null;
    }
    /**
     * @param $key
     * @param $data
     * @param $lifetime
     * @throws Zend_Cache_Exception
     */
    private function saveRAW2Cache($key, $data, $lifetime)
    {
        $this->getCache()->save(serialize($data), md5($key), [], $lifetime);
    }
    /**
     * @return string
     * @throws Exception
     */
    private function getAccessToken():string {
        $access_token = $this->loadRAWFromCache("access_token");
        if(!empty($access_token)) {
            if(Director::is_cli()){
                fwrite(STDOUT, sprintf("%s - [MeetupApi::getAccessToken] got access token %s from cache", gmdate('Y-m-d h:i:s \G\M\T', time()), $access_token).PHP_EOL);
            }
            return $access_token;
        }
        if(Director::is_cli()){
            fwrite(STDOUT, sprintf("%s - [MeetupApi::getAccessToken] access token not present on cache", gmdate('Y-m-d h:i:s \G\M\T', time()) ).PHP_EOL);
        }
        $response = $this->client->get(self::BaseAuthUrl."/oauth2/authorize" , [
            'headers' => [
                'Accept' => 'application/json'
            ],
            'query' => [
                'client_id'     => MEETUP_OAUTH_CLIENT_ID,
                'redirect_uri'  => MEETUP_OAUTH_REDIRECT_URL,
                'response_type' => 'anonymous_code',
            ]
        ]);
        if($response->getStatusCode() !== 200)
            throw new Exception('invalid status code!');
        $content_type = $response->getHeaderLine('content-type');
        if(empty($content_type))
            throw new Exception('invalid content type!');
        if(!strstr($content_type,'application/json'))
            throw new Exception('invalid content type!');
        $json_content  = $response->getBody()->getContents();
        $json_response = json_decode($json_content, true);
        if(!key_exists('code', $json_response))
            throw new InvalidArgumentException();
        $code = $json_response['code'];
        $response = $this->client->post(self::BaseAuthUrl."/oauth2/access" , [
            'headers' => [
                'Accept' => 'application/json'
            ],
            'query' => [
                'client_id'     => MEETUP_OAUTH_CLIENT_ID,
                'client_secret' => MEETUP_OAUTH_CLIENT_SECRET,
                'redirect_uri'  => MEETUP_OAUTH_REDIRECT_URL,
                'grant_type'    => 'anonymous_code',
                'code'          => $code,
            ]
        ]);
        if($response->getStatusCode() !== 200)
            throw new Exception('invalid status code!');
        $content_type = $response->getHeaderLine('content-type');
        if(empty($content_type))
            throw new Exception('invalid content type!');
        if(!strstr($content_type,'application/json'))
            throw new Exception('invalid content type!');
        $json_content  = $response->getBody()->getContents();
        $json_response = json_decode($json_content, true);
        if(!key_exists('access_token', $json_response))
            throw new InvalidArgumentException();
        $access_token  = $json_response['access_token'];
        $response = $this->client->post(self::BaseUrl."/sessions" , [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => sprintf("Bearer %s", $access_token)
            ],
            'query' => [
                'email'    => MEETUP_USER,
                'password' => MEETUP_PASSWORD,
            ]
        ]);
        if($response->getStatusCode() !== 200)
            throw new Exception('invalid status code!');
        $content_type = $response->getHeaderLine('content-type');
        if(empty($content_type))
            throw new Exception('invalid content type!');
        if(!strstr($content_type,'application/json'))
            throw new Exception('invalid content type!');
        $json_content  = $response->getBody()->getContents();
        $json_response = json_decode($json_content, true);
        if(!key_exists('oauth_token', $json_response))
            throw new InvalidArgumentException();
        $access_token  = $json_response['oauth_token'];
        $lifetime = intval($json_response['expires_in']) * 0.9;
        if(Director::is_cli()){
            fwrite(STDOUT, sprintf("%s - [MeetupApi::getAccessToken] saving access token %s to cache with lifetime %s", gmdate('Y-m-d h:i:s \G\M\T', time()), $access_token, $lifetime).PHP_EOL);
        }
        $this->saveRAW2Cache('access_token', $access_token, $lifetime);
        return $access_token;
    }
    public function __construct()
    {
        $this->client = new Client();
    }
    /**
     * @param int $page
     * @return array
     * @throws Exception
     */
    public function getAllUpcomingEvents(int $page = 20):array {
        $access_token = $this->getAccessToken();
        $headers = [
            'Authorization' => sprintf("Bearer %s", $access_token),
            'Accept'        => 'application/json',
        ];
        $query = "
            {
              self {
                id
                upcomingEvents {
                  count
                  pageInfo {
                    endCursor
                  }
                  edges {
                    node {
                      id
                      title
                      description
                    }
                  }
                }
              }
            }
        ";
        // to avoid error http 429 Credentials have been throttled
        usleep(self::Throttle);
        $response = $this->client->post(self::BaseUrl.'/gql', [
                'headers' => $headers,
                \GuzzleHttp\RequestOptions::JSON => [
                    'query' => $query
                ]
            ]
        );
        if($response->getStatusCode() !== 200)
            throw new Exception('invalid status code!');
        $content_type = $response->getHeaderLine('content-type');
        if(empty($content_type))
            throw new Exception('invalid content type!');
        if(!strstr($content_type,'application/json'))
            throw new Exception('invalid content type!');
        $json = $response->getBody()->getContents();
        $json_content = json_decode($json, true);

        $events = array_map(
            function ($event) {
                return $event['node'];
            },
            $json_content['data']['self']['upcomingEvents']['edges']
        );


        return $events;
    }
}