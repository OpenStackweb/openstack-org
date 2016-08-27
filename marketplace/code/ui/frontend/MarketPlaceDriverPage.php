<?php
/**
 * Copyright 2014 Openstack Foundation
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
 * Class MarketPlaceDriverPage
 */
final class MarketPlaceDriverPage extends MarketPlaceDirectoryPage {

}

/**
 * Class MarketPlaceDriverPage_Controller
 */
final class MarketPlaceDriverPage_Controller extends MarketPlaceDirectoryPage_Controller {


    function init() {
        parent::init();
        Requirements::javascript("marketplace/code/ui/frontend/js/driver.page.js");
    }

    public static function DriverTable($project = null){
        if ($project) {
            return Driver::get()->filter(array('Project' => $project, 'Active' => 1));
        }

        return Driver::get()->filter('Active', 1);
    }

    public function getProjects() {
        return GroupedList::create(Driver::get()->filter('Active', 1)->sort('Project'))->GroupedBy('Project');
    }

}