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

/**
 * Class Robots_Controller
 * @see http://www.robotstxt.org/robotstxt.html
 */
final class Robots_Controller extends Controller
{

    private static $allowed_actions = array(
        'index',
    );

    public function index($request)
    {

        $domain   = Director::absoluteBaseURL();
        $extra  = '';
        if(Director::isDev()){
            $extra = 'Disallow: /';
        }

        $body = <<< ROBOTS
User-agent: *
{$extra}
Sitemap: {$domain}sitemap.xml
ROBOTS;

        $response = new SS_HTTPResponse($body, 200);
        $response->addHeader('Content-Type', ' text/plain');
        return $response;
    }
}