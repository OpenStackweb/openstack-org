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
        Requirements::javascript('themes/openstack/javascript/urlfragment.jquery.js');
    }

    private function getActiveDrivers() {
        return Driver::get()->filter('Active', 1);
    }

    public function getProjects() {
        $drivers = $this->getActiveDrivers()->sort('Project')->column('Project');
        $projects = [];

        foreach ($drivers as $project) {
            $projects[] = new ArrayData(['Name' => $project]);
        }

        return new ArrayList($projects);
    }

    public function getReleases() {
        $drivers = $this->getActiveDrivers();
        $releases = [];
        $release_list = [];

        foreach ($drivers as $driver) {
            foreach ($driver->Releases()->column('Name') as $release) {
                $releases[] = $release;
            }
        }

        $releases = array_unique($releases);
        sort($releases);

        foreach ($releases as $release) {
            $release_list[] = new ArrayData(['Name' => $release]);
        }

        return new ArrayList($release_list);
    }

    public function getVendors() {
        $drivers = $this->getActiveDrivers()->sort('Vendor')->column('Vendor');
        $vendors = [];

        foreach ($drivers as $vendor) {
            $vendors[] = new ArrayData(['Name' => $vendor]);
        }

        return new ArrayList($vendors);
    }

}