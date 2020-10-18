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

/**
 * Class JQueryValidateDependencies
 */
final class JQueryValidateDependencies
{
    public static function renderRequirements($use_custom_css = true, $use_datepicker_validation = true){

        if($use_custom_css)
            Requirements::css("themes/openstack/css/validation.errors.css");

        if(Director::isLive()) {
            Requirements::javascript("node_modules/jquery-validation/dist/jquery.validate.min.js");
            if($use_datepicker_validation)
                Requirements::javascript("themes/openstack/javascript/jquery.ui.datepicker.validation.package-1.0.1/jquery.ui.datepicker.validation.min.js");
        }
        else{
            Requirements::javascript("node_modules/jquery-validation/dist/jquery.validate.js");
            if($use_datepicker_validation)
                Requirements::javascript("themes/openstack/javascript/jquery.ui.datepicker.validation.package-1.0.1/jquery.ui.datepicker.validation.js");
        }

        Requirements::javascript("node_modules/jquery-validation/dist/additional-methods.js");
        Requirements::javascript("themes/openstack/javascript/jquery.validate.custom.methods.js");
    }
}