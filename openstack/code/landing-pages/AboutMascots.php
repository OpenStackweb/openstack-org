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
class AboutMascots extends Page {
   static $db = array(
	);
}
 
class AboutMascots_Controller extends Page_Controller {

    private static $mascots_dir = 'themes/openstack/images/project-mascots/';

    function init()
    {
        parent::init();

        Requirements::CSS('themes/openstack/css/about-mascots.css');

        Requirements::javascript('themes/openstack/javascript/filetracking.jquery.js');

        Requirements::customScript("var mascots_dir = '".self::$mascots_dir."';");

        Requirements::javascript('themes/openstack/javascript/about-mascots.js');

    }

    function getComponents() {
        $components = OpenStackComponent::get()->sort('CodeName');
        $componentsAL = new ArrayList();
        foreach ($components as $component) {
            $mascots_folder = Director::baseFolder() .'/'. self::$mascots_dir . $component->CodeName;
            $image_array = array();
            foreach (glob($mascots_folder.'/*.*') as $image) {
                $image_array[] = basename($image);
            }
            $component->MascotFiles = implode(',', $image_array);
            $componentsAL->push($component);
        }
        return $componentsAL;
    }

}
 
?>