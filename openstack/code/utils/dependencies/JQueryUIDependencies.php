<?php
/**
 * Copyright 2017 Open Infrastructure Foundation
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

final class JQueryUIDependencies
{

    const SmoothnessTheme = "smoothness";
    const DefaultTheme    = "default";

    public static function renderRequirements($theme = null){

        switch ($theme){
            case self::SmoothnessTheme:
            {
                Requirements::css("themes/openstack/javascript/jquery-ui/themes/smoothness/jquery-ui.min.css");
            }
            break;
            case self::DefaultTheme:
            {
                Requirements::css("themes/openstack/javascript/jquery-ui/themes/base/jquery-ui.min.css");
            }
            break;
        }
        if(Director::isLive()) {
            Requirements::javascript("themes/openstack/javascript/jquery-ui/jquery-ui.min.js");
        }
        else{
            Requirements::javascript("themes/openstack/javascript/jquery-ui/jquery-ui.js");
        }
        // https://www.ryadel.com/en/using-jquery-ui-bootstrap-togheter-web-page/
        Requirements::javascript("themes/openstack/javascript/jquery-ui-bridge.js");
    }


}