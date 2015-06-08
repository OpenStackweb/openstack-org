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

final class OpenStackIdCommon {

    public static function redirectToSSL($url){
        $dest = str_replace('http:', 'https:', Director::absoluteURL($url));
        // This coupling to SapphireTest is necessary to test the destination URL and to not interfere with tests
        if(!headers_sent()) header("Location: $dest");
        die("<h1>Your browser is not accepting header redirects</h1><p>Please <a href=\"$dest\">click here</a>");
    }

    public static function getReturnTo()
    {
        $trust_root    = self::getTrustRoot();
        $return_to_url = $trust_root . '/OpenStackIdAuthenticator?url=/OpenStackIdAuthenticator';
        if(Controller::curr()->getRequest()->getVar('BackURL')){
            $return_to_url .= '&BackURL='.Controller::curr()->getRequest()->getVar('BackURL');
        }
        return $return_to_url;
    }

    public static function getTrustRoot()
    {
        return Auth_OpenID_Realm;
    }

    public static function escape($thing) {
        return htmlentities($thing);
    }

    public static function getRedirectBackUrl(){
        $url = null;
        // Don't cache the redirect back ever
        HTTP::set_cache_age(0);
        // In edge-cases, this will be called outside of a handleRequest() context; in that case,
        // redirect to the homepage - don't break into the global state at this stage because we'll
        // be calling from a test context or something else where the global state is inappropraite
        if($request = Controller::curr()->getRequest()) {
            if($request->requestVar('BackURL')) {
                $url = $request->requestVar('BackURL');
            } else if($request->isAjax() && $request->getHeader('X-Backurl')) {
                $url = $request->getHeader('X-Backurl');
            } else if($request->getHeader('Referer')) {
                $url = $request->getHeader('Referer');
            }
        }

        if(!$url) $url = Director::baseURL();
        if(strpos($url,'/Security/login') !== false ) $url = Director::baseURL();
        return $url;
    }
}