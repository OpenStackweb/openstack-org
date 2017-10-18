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

final class SangriaMarketplaceRouterApiExtension extends DataExtension
{
    const SubRouteMarketplace = 'marketplace';

    static $allowed_subroutes = [
        self::SubRouteMarketplace
    ];

    public function handleSubRoute($subroute, SS_HTTPRequest $request){
        if(!empty($subroute) && in_array($subroute, self::$allowed_subroutes)){
            $api = null;
            switch($subroute){
                // here define the sub route handling
                case self::SubRouteMarketplace:
                    $api = SangriaMarketPlaceRestfullApi::create();
                    break;
            }
            if(!is_null($api))
                return $api->handleRequest($request, DataModel::inst());
        }
    }
}