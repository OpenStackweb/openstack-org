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
 * Class TCPCloudRestApi
 */
final class TCPCloudRestApi implements ITCPCloudRestApi
{

    /**
     * @param string $endpoint
     * @param int $since_time_stamp
     * @return array
     * @throws Exception
     */
    public function getSamplesDataFromEndpointSinceTimestamp($endpoint, $since_time_stamp)
    {
        $client   = new GuzzleHttp\Client();
        $response = $client->get($endpoint);

        if($response->getStatusCode() !== 200) throw new Exception('invalid status code!');
        $content_type = $response->getHeader('content-type');
        if(empty($content_type)) throw new Exception('invalid content type!');
        if($content_type !== 'application/json') throw new Exception('invalid content type!');

        $json       = $response->getBody()->getContents();
        $response   = json_decode($json, true);
        if(!isset($response['datapoints'])) throw new Exception('missing response datapoints!');
        $datapoints = array();

        foreach ($response['datapoints'] as $datapoint) {
            if(count($datapoint) != 2) continue;
            $time_stamp = $datapoint[1];
            if($time_stamp <= $since_time_stamp) continue;
            $datapoints[] = $datapoint;
        }
        return $datapoints;
    }
}