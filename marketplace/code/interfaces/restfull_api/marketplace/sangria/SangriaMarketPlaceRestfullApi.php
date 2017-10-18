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

final class SangriaMarketPlaceRestfullApi extends AbstractRestfulJsonApi
{
    /**
     * @return bool
     */
    public function authorize(){
        //check permissions
        if(!Permission::check("SANGRIA_ACCESS"))
            return false;
        return true;
    }

    static $url_handlers = [
        ' cloud-services'                    => 'handleClouds',
        ' openstack-powered-implementations' => 'handleOpenStackPoweredImplementations',
        ' regional-services'                 => 'handleRegionalServices',
    ];

    static $allowed_actions = [
        'handleClouds',
        'handleOpenStackPoweredImplementations',
        'handleRegionalServices',
    ];

    function handleClouds(SS_HTTPRequest $request){
        $api = SangriaMarketPlaceCloudsRestfullApi::create();
        return $api->handleRequest($request, DataModel::inst());
    }

    function handleOpenStackPoweredImplementations(SS_HTTPRequest $request){
        $api = SangriaMarketPlaceOpenStackPoweredImplementionResfullApi::create();
        return $api->handleRequest($request, DataModel::inst());
    }

    function handleRegionalServices(SS_HTTPRequest $request){
        $api = SangriaMarketPlaceRegionalCompanyServiceResfullApi::create();
        return $api->handleRequest($request, DataModel::inst());
    }
}