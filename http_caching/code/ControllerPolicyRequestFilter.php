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
 * This request filter accepts registrations of policies to be applied at the end of the control pipeline.
 * The policies will be applied in the order they are added, and will override HTTP::add_cache_headers.
 */

class ControllerPolicyRequestFilter implements RequestFilter {

    /**
     * @var array $ignoreDomainRegexes Force some domains to be ignored. Accepts one wildcard at the beginning.
     */
    private static $ignoreDomainRegexes = array();


    /**
     * An associative array containing the 'originator' and 'policy' reference.
     */
    private $requestedPolicies = array();

    /**
     * Check if the given domain is on the list of ignored domains.
     */
    public function isIgnoredDomain($domain) {

        if ($ignoreRegexes = Config::inst()->get('ControllerPolicyRequestFilter', 'ignoreDomainRegexes')) {
            foreach ($ignoreRegexes as $ignore) {
                if (preg_match($ignore, $domain)>0) return true;
            }
        }

        return false;

    }

    /**
     * Add a policy tuple.
     */
    public function requestPolicy($originator, $policy) {
        $this->requestedPolicies[] = array('originator' => $originator, 'policy' => $policy);
    }

    public function clearPolicies() {
        $this->requestedPolicies = array();
    }

    public function preRequest(SS_HTTPRequest $request, Session $session, DataModel $model) {

        // No-op, we don't know the controller at this stage.
        return true;

    }

    /**
     * Apply all the requested policies.
     */
    public function postRequest(SS_HTTPRequest $request, SS_HTTPResponse $response, DataModel $model) {

        // Ingore by regexes.
        if ($this->isIgnoredDomain($_SERVER['HTTP_HOST'])) {
            return true;
        }

        foreach ($this->requestedPolicies as $requestedPolicy) {

            $requestedPolicy['policy']->applyToResponse(
                $requestedPolicy['originator'],
                $request,
                $response,
                $model
            );

        }

        return true;

    }

}
