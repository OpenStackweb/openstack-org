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
 * Class MobileAppLinksController
 *
 * https://developer.apple.com/library/ios/documentation/General/Conceptual/AppSearch/UniversalLinks.html
 * https://developer.android.com/training/app-links/index.html
 */
final class MobileAppLinksController extends Controller
{
    static $url_handlers = array
    (
        'GET apple-app-site-association' => 'getAppleAppSiteAssociationFile',
        'GET assetlinks.json'            => 'getAndroidAssetLinksFile',
    );

    static $allowed_actions = array
    (
        'getAppleAppSiteAssociationFile',
        'getAndroidAssetLinksFile',
    );

    public function getAppleAppSiteAssociationFile(SS_HTTPRequest $request)
    {
        global $APP_LINKS_IOS_FILE_CONFIG;

        $file = ['applinks' => [
            "apps" => [],
            "details" => [],
        ]];

        foreach($APP_LINKS_IOS_FILE_CONFIG as $app_id => $paths){
            $file['applinks']['details'] []= [
                "appID" => $app_id,
                "paths" => $paths
            ];
        }

        $response = new SS_HTTPResponse(json_encode($file), 200);
        $response->addHeader('Content-Type', 'application/json; charset=utf-8');
        return $response;

    }

    public function getAndroidAssetLinksFile(SS_HTTPRequest $request)
    {
        global $APP_LINKS_ANDROID_FILE_CONFIG;

        $file = [];

        foreach($APP_LINKS_ANDROID_FILE_CONFIG as $package => $fingerprints){
            $file []= [
                "relation" => ["delegate_permission/common.handle_all_urls"],
                "target"   => [
                    "namespace"                => "android_app",
                    "package_name"             =>  $package,
                    "sha256_cert_fingerprints" => $fingerprints
                ]
            ];
        }

        $response = new SS_HTTPResponse(json_encode($file), 200);
        $response->addHeader('Content-Type', 'application/json; charset=utf-8');
        return $response;
    }
}