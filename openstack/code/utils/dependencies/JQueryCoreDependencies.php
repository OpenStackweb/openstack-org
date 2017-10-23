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

final class JQueryCoreDependencies
{
    public static function renderRequirements(){
        Requirements::block(SAPPHIRE_DIR . "/javascript/jquery_improvements.js");
        Requirements::block(FRAMEWORK_DIR . '/thirdparty/jquery/jquery.js');
        Requirements::block(FRAMEWORK_DIR . '/thirdparty/jquery/jquery.min.js');
        Requirements::block(THIRDPARTY_DIR . '/jquery-cookie/jquery.cookie.js');

        if(Director::isLive()) {
            Requirements::javascript('node_modules/jquery/dist/jquery.min.js');
            Requirements::javascript("node_modules/jquery-migrate/dist/jquery-migrate.min.js");
        }
        else{
            Requirements::javascript('node_modules/jquery/dist/jquery.js');
            Requirements::javascript("node_modules/jquery-migrate/dist/jquery-migrate.js");
        }
    }
}