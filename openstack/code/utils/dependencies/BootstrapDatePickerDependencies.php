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

final class BootstrapDatePickerDependencies
{
    public static function renderRequirements(){

        if(Director::isLive()) {
            Requirements::css("node_modules/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css");
            Requirements::javascript('node_modules/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js');
            return;
        }
        Requirements::css("node_modules/bootstrap-datepicker/dist/css/bootstrap-datepicker3.css");
        Requirements::javascript('node_modules/bootstrap-datepicker/dist/js/bootstrap-datepicker.js');
    }
}