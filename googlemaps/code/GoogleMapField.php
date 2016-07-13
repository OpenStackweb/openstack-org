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
 * Class GoogleMapField
 */
class GoogleMapField extends LiteralField
{

    public function __construct($name, $options = array())
    {

        // Set map defaults
        $defaults = [
            "width"         => "100%",
            "height"        => "500px",
            "heading"       => "",
            "lng_field"     => "Form_ItemEditForm_Lng",
            "lat_field"     => "Form_ItemEditForm_Lat",
            "tab"           => "Root_Location",
            "address_field" => "Address",
            "map_zoom"      => 18,
            "start_lat"     => "51.508515",
            "start_lng"     => "-0.125487"
        ];

        // Merge provided options with defaults to create params
        $params = array_replace_recursive($defaults, $options);

        // Set css of map
        $css = "style='width: " . $params['width'] . "; height: " . $params['height'] . ";'";

        if(defined('GOOGLE_MAP_KEY') && empty($google_map_api_key))
            $google_map_api_key = GOOGLE_MAP_KEY;

        if(empty($google_map_api_key))
            throw new InvalidArgumentException('you must provide a valid google maps api key!');

        // Set up array to be fed to the JS
        $js = [
            "lat_field"     => $params['lat_field'],
            "lng_field"     => $params['lng_field'],
            "tab"           => $params['tab'],
            "address_field" => $params['address_field'],
            "zoom"          => $params['map_zoom'],
            "start_lat"     => $params['start_lat'],
            "start_lng"     => $params['start_lng'],
            "key"           => $google_map_api_key
        ];

        // Build content of form field

        $content = "";

        if ($params['heading']) {
            $content .= "<h4>" . $params['heading'] . "</h4>";
        }

        $content .= "<div id='admin-map-" . $name . "' class='admin-google-map' " . $css . " data-setup='" . json_encode($js) . "'></div>";

        $this->content = $content;

        // Establish requirements
        Requirements::javascript(ADMIN_GOOGLE_MAP_DIR . "/js/admin-google-map.js");

        if (!$this->stat('jquery_included')) {
            Requirements::javascript(THIRDPARTY_DIR . "/jquery/jquery.js");
        }

        parent::__construct($name, $this->content);

    }
}
