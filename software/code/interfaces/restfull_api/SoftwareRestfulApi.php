<?php

/**
 * Copyright 2015 OpenStack Foundation
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
class SoftwareRestfulApi extends AbstractRestfulJsonApi
{
    const ApiPrefix = 'api/v1/software';

    /**
     * @var ISoftwareManager
     */
    private $manager;

    /**
     * @return ISoftwareManager
     */
    public function getSoftwareManager()
    {
        return $this->manager;
    }

    /**
     * @param ISoftwareManager $manager
     */
    public function setSoftwareManager(ISoftwareManager $manager)
    {
        $this->manager = $manager;
    }

    protected function isApiCall(){
        $request = $this->getRequest();
        if(is_null($request)) return false;
        return  strpos(strtolower($request->getURL()),self::ApiPrefix) !== false;
    }

    /**
     * @return bool
     */
    protected function authorize(){
        return true;
    }


    private static $allowed_actions = array
    (
        'getComponentsbyRelease',
    );

    static $url_handlers = array
    (
        'GET releases/$RELEASE_ID/components'      => 'getComponentsbyRelease',
    );

    public function getComponentsbyRelease(SS_HTTPRequest $request)
    {
        $release_id = intval($request->param('RELEASE_ID'));
        $term       = Convert::raw2sql($request->getVar('term'));
        $adoption   = intval(Convert::raw2sql($request->getVar('adoption')));
        $maturity   = intval(Convert::raw2sql($request->getVar('maturity')));
        $age        = intval(Convert::raw2sql($request->getVar('age')));

        $release = OpenStackRelease::get()->byID($release_id);
        if(is_null($release))
            return $this->notFound();

        list($core_components, $optional_components) = $this->manager->getComponents
        (
            $release,
            $term,
            $adoption,
            $maturity,
            $age
        );

        $res = array
        (
            'core_components'     => $core_components,
            'optional_components' => $optional_components
        );

        return $this->ok($res);

    }

}