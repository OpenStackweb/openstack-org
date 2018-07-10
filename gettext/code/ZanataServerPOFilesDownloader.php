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
use GuzzleHttp\Client;
/**
 * Class ZanataServerPOFilesDownloader
 */
final class ZanataServerPOFilesDownloader implements IZanataServerPOFilesDownloader
{

    /**
     * @var Client
     */
    private $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * @param string $module
     * @param string $project_id
     * @param array $files
     * @return void
     */
    public function downloadPOFiles($module, $project_id, $files = [])
    {
        $dir = Director::baseFolder() ."/".$module."/Locale/%s/LC_MESSAGES/";
        foreach ($files as $file) {

            if (!file_exists(sprintf($dir, $file['lang_local']))) {
                mkdir(sprintf($dir, $file['lang_local']),  $mode = 0775, $recursive = true);
            }

            $filename  = sprintf($dir, $file['lang_local']).'/'.$file['doc_id'].'.po';
            $file_url  = sprintf("https://translate.openstack.org/rest/file/translation/%s/%s/%s/po?docId=%s", $project_id, $project_id, $file['lang_zanata'], $file['doc_id']);
            echo sprintf("downloading file %s", $file_url).PHP_EOL;
            $response  = $this->client->get(
                $file_url,
                ['sink' => $filename]
            );
            echo sprintf("downloaded file %s - http response code %s", $file_url, $response->getStatusCode()).PHP_EOL;
        }
    }
}