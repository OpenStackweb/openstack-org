<?php
/**
 * Copyright 2014 Openstack Foundation
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
class AboutMascots extends Page {
   static $db = array(
	);
}
 
class AboutMascots_Controller extends Page_Controller {

    function init()
    {
        parent::init();

        Requirements::CSS('themes/openstack/css/about-mascots.css');

        Requirements::javascript('themes/openstack/javascript/filetracking.jquery.js');

        Requirements::customScript("
            var mascots_dir = '".CloudAssetTemplateHelpers::cloud_url(Mascot::$mascots_dir)."';
            var base_url = '".Director::absoluteBaseURL()."';
        ");

        Requirements::javascript('themes/openstack/javascript/about-mascots.js');

    }

    static function getCache(){
        return SS_Cache::factory(strtolower( 'AboutMascots_mascots_cache'));
    }

    static function processMascots($force = false) {

        $mascots = Mascot::get();
        $mascotsAL = new ArrayList();
        $client = new \GuzzleHttp\Client();

        foreach ($mascots as $mascot) {

            $mascot_folder = $mascot->getRelativeImageDir();
            $mascot->MascotFiles     = '';
            $mascot->CodeNameString = $mascot->CodeName;

            if ($mascot_folder) {
                // ask if we have cached values
                if(!$force && $value = self::getCache()->load(md5($mascot->CodeName))) {
                    $value                    = unserialize($value);
                    $mascot->MascotFiles      = $value['MascotFiles'];
                    $mascot->EPSThumbFileUrl  = $value['EPSThumbFileUrl'];
                }
                else {
                    $bucket = CloudAssets::inst()->map($mascot_folder);
                    if ($bucket) {
                        $query = sprintf("%s?delimiter=/&prefix=%s/%s/", $bucket->getBaseURL(), Mascot::$mascots_folder, $mascot->CodeName);
                        $response = $client->request('GET', $query);
                        $image_array = [];
                        $body = (string)$response->getBody();
                        foreach (explode("\n", $body) as $file_url) {
                            $parts = explode("/", $file_url);
                            if (strpos($parts[count($parts) - 1], ".") !== false) {
                                $image_array[] = sprintf("%s%s", $bucket->getBaseURL(), $file_url);
                            }
                        }
                        $mascot->MascotFiles = implode(',', $image_array);
                        $mascot->EPSThumbFileUrl = sprintf("%s%s/eps_thumb.png", $bucket->getBaseURL(), Mascot::$mascots_folder);
                        $data = [
                            'MascotFiles' => $mascot->MascotFiles,
                            'EPSThumbFileUrl' => $mascot->EPSThumbFileUrl
                        ];
                        // store on cache
                        self::getCache()->save
                        (
                            serialize($data),
                            md5($mascot->CodeName),
                            $tags = [],
                            $specificLifetime = 3600 * 24 // 24 hours
                        );
                    }
                }
            }
            $mascotsAL->push($mascot);
        }
        return $mascotsAL;
    }

    function getMascots() {
        $mascotsAL = self::processMascots();
        return $mascotsAL->sort('CodeNameString');
    }

}
 
?>