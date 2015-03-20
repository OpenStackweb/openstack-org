<?php
/**
 * Copyright 2015 Openstack Foundation
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
 * Class CachingPolicy
 * http://www.w3.org/Protocols/rfc2616/rfc2616-sec13.html#sec13.3
 *
 */
class CustomHTTPCachingPolicy extends HTTP implements ControllerPolicy
{
    /**
     * Extends HTTP to get access to globals describing the Last-Modified and Etag data.
     */

    /**
     * @var int $cacheAge Max-age seconds to cache for if configuration not available from the originator.
     */
    public $cacheAge = 0;

    /**
     * @var string $vary Vary string to add if configuration is not available from the originator.
     *        Note on vary headers: Do not add user-agent unless you vary on it AND you have configured user-agent
     *        clustering in some way, otherwise this will be an equivalent to disabling caching as there
     *        is a lot of different UAs in the wild.
     */
    public $vary = 'Cookie, X-Forwarded-Protocol';

    public function applyToResponse($originator, SS_HTTPRequest $request, SS_HTTPResponse $response, DataModel $model)
    {

        $responseHeaders = array();
        $cacheAge = $this->cacheAge;
        $vary = $this->vary;

        // Allow overriding max-age from the object hooked up to the policed controller.
        if ($originator->hasMethod('getCacheAge')) {
            $extendedCacheAge = $originator->getCacheAge($cacheAge);
            if ($extendedCacheAge !== null) $cacheAge = $extendedCacheAge;
        }

        // Same for vary, but probably less useful.
        if ($originator->hasMethod('getVary')) {
            $extendedVary = $originator->getVary($vary);
            if ($extendedVary !== null) $vary = $extendedVary;
        }

        $etag = null;

        if ($cacheAge > 0) {
            // Note: must-revalidate means that the cache must revalidate AFTER the entry has gone stale.
            $responseHeaders["Cache-Control"] = "max-age=" . $cacheAge . ", must-revalidate, no-transform";
            $responseHeaders["Pragma"] = "";
            $responseHeaders['Vary'] = $vary;

            // Find out when the URI was last modified. Allows customisation, but fall back HTTP timestamp collector.
            if ($originator->hasMethod('getModificationTimestamp')) {
                $timestamp = $originator->getModificationTimestamp();
            } else {
                $timestamp = HTTP::$modification_date;
            }

            if ($timestamp) {
                $responseHeaders["Last-Modified"] = self::gmt_date($timestamp);
                $expires = time() + $cacheAge;
                $responseHeaders["Expires"] = self::gmt_date($expires);
            }

            // first etag calculation based on timestamp and cookies ....
            // Chrome ignores Varies when redirecting back (http://code.google.com/p/chromium/issues/detail?id=79758)
            // which means that if you log out, you get redirected back to a page which Chrome then checks against
            // last-modified (which passes, getting a 304)
            // when it shouldn't be trying to use that page at all because it's the "logged in" version.
            // By also using and etag that includes both the modification date and all the varies
            // values which we also check against we can catch this and not return a 304
            $etagParts = array($timestamp, serialize($_COOKIE));
            $etagParts[] = Director::is_https() ? 'https' : 'http';
            if (isset($_SERVER['HTTP_USER_AGENT'])) $etagParts[] = $_SERVER['HTTP_USER_AGENT'];
            if (isset($_SERVER['HTTP_ACCEPT'])) $etagParts[] = $_SERVER['HTTP_ACCEPT'];

            $etag = sha1(implode(':', $etagParts));
            $responseHeaders["ETag"] = $etag;
        }

        // custom calculation os ETag
        if ($originator->hasMethod('getEtag')) {
            $extendedEtag = $originator->getEtag($response->getBody());
            if ($extendedEtag !== null) {
                $etag = $extendedEtag;
                $responseHeaders["ETag"] = $etag;
            }
        }

        // Now that we've generated them, either output them or attach them to the SS_HTTPResponse as appropriate
        foreach ($responseHeaders as $k => $v) {
            $response->addHeader($k, $v);
        }

        // 304 response detection

        /**
         * The HTTP IF_MODIFIED_SINCE request-header, is a header which is sent by most browsers.
         * It contains the modification date provided in the Last-Modified header by the server on the last visit.
         */
        if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
            $ifModifiedSince = strtotime(stripslashes($_SERVER['HTTP_IF_MODIFIED_SINCE']));
            // As above, only 304 if the last request had all the same varies values
            // (or the etag isn't passed as part of the request - but with chrome it always is)
            $matchesEtag = !isset($_SERVER['HTTP_IF_NONE_MATCH']) || $_SERVER['HTTP_IF_NONE_MATCH'] == $etag;
            if ($ifModifiedSince >= $timestamp && $matchesEtag) {
                ob_clean();
                $response->setStatusCode(304);
                $response->setBody('');
            }
        }
        else if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] == $etag) {
            ob_clean();
            $response->setStatusCode(304);
            $response->setBody('');
        }

    }

}