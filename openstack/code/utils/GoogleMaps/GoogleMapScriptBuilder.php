<?php
/**
 * Copyright 2018 Open Infrastructure Foundation
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
 * Class GoogleMapScriptBuilder
 */
final class GoogleMapScriptBuilder
{
    /**
     * @param string|null $sensor
     * @param string|null $version
     */
    public static function renderRequirements($sensor = null, $version = null){

        if(!defined('GOOGLE_MAP_KEY'))
            throw new InvalidArgumentException('you must provide a valid google maps api key (GOOGLE_MAP_KEY) !');
        // base url
        $google_map_lib_url = sprintf(Director::protocol()."maps.googleapis.com/maps/api/js?key=%s", GOOGLE_MAP_KEY);

        if(!empty($sensor)){
            $google_map_lib_url .=  "&sensor=".$sensor;
        }

        if(!empty($version)){
            $google_map_lib_url .= "&v=".$version;
        }

        Requirements::javascript($google_map_lib_url);
    }


    public static function renderMarkersClustered(){
        self::renderRequirements();
        Requirements::javascript("googlemaps/js/markerclusterer.js");
        Requirements::javascript("googlemaps/js/oms.min.js");
        Requirements::javascript("googlemaps/js/infobubble-compiled.js");
        Requirements::javascript("googlemaps/js/google.maps.jquery.js");
    }
}