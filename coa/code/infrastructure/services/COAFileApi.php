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
use Guzzle\Http\Client;

/**
 * Class COAFileApi
 */
final class COAFileApi implements ICOAFileApi
{

    /**
     * @var Client
     */
    private $client;

    /**
     * COAFileApi constructor.
     */
    public function __construct()
    {
        // Create a client and provide a base URL
        $this->client = new Client(COA_FILE_API_BASE_URL);
    }

    /**
     * @return string[]
     */
    public function getFilesList()
    {
        $request = $this->client->get('/exports/openstackcert');
        $request->setAuth(COA_FILE_API_BASE_USER, COA_FILE_API_BASE_PASS);
        $response = $request->send();
        $html     = (string)$response->getBody();
        $doc      = new DOMDocument();
        $doc->preserveWhitespace = false;
        $doc->loadHTML($html);
        $file_links = $doc->getElementsByTagName('a');
        $files = array();
        $pattern =  '/^coa-export-(\d+)\.csv/';
        foreach($file_links as $af)
        {
            $href= $af->getAttribute('href');
            if(!preg_match($pattern, $href, $matches, PREG_OFFSET_CAPTURE)) continue;
            if(count($matches) < 2) continue;

            $files[] = array
            (
                'filename'  => $matches[0][0],
                'timestamp' => intval($matches[1][0])
            );
        }
        uasort($files, array($this, 'orderFiles'));
        return $files;
    }

    private function orderFiles($a, $b)
    {
        if($a['timestamp'] == $b['timestamp']) {
            return 0;
        }
        return ($a['timestamp'] > $b['timestamp']) ? 1 : -1;
    }

    /**
     * @param string $file
     * @return string
     */
    public function getFileContent($file)
    {
        $request = $this->client->get('/exports/openstackcert/'.$file);
        $request->setAuth(COA_FILE_API_BASE_USER, COA_FILE_API_BASE_PASS);
        $response = $request->send();
        $content  = (string)$response->getBody();
        return $content;
    }
}