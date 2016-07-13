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

trait GoogleMapLibs
{
    /**
     * @param null $google_map_api_key
     */
    public function InitGoogleMapLibs($google_map_api_key = null){

        if(defined('GOOGLE_MAP_KEY') && empty($google_map_api_key))
            $google_map_api_key = GOOGLE_MAP_KEY;

        if(empty($google_map_api_key))
            throw new InvalidArgumentException('you must provide a valid google maps api key!');

        $google_map_lib_url = sprintf("maps.googleapis.com/maps/api/js?key=%s", $google_map_api_key);
        Requirements::javascript(Director::protocol().$google_map_lib_url);
        // bower install js-marker-clusterer
        Requirements::javascript("googlemaps/js/markerclusterer.js");
        Requirements::javascript("googlemaps/js/oms.min.js");
        Requirements::javascript("googlemaps/js/infobubble-compiled.js");
        Requirements::javascript("googlemaps/js/google.maps.jquery.js");
    }
}