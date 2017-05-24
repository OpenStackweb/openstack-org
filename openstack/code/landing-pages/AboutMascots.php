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

    function init()
    {
        parent::init();

        Requirements::CSS('themes/openstack/css/about-mascots.css');

        Requirements::javascript('themes/openstack/javascript/filetracking.jquery.js');

        Requirements::customScript("
            var mascots_dir = '".Mascot::$mascots_dir."';
            var base_url = '".Director::absoluteBaseURL()."';
        ");

        Requirements::javascript('themes/openstack/javascript/about-mascots.js');

    }

    function getMascots() {
        $mascots = Mascot::get();

        $mascotsAL = new ArrayList();
        foreach ($mascots as $mascot) {
            $mascot_folder = $mascot->getImageDir();
            $mascot->MascotFiles = '';
            $mascot->CodeNameString = $mascot->CodeName;

            if ($mascot_folder) {
                $image_array = array();
                foreach (glob($mascot_folder.'/*.*') as $image) {
                    $image_array[] = basename($image);
                }
                $mascot->MascotFiles = implode(',', $image_array);
            }

            $mascotsAL->push($mascot);
        }

        return $mascotsAL->sort('CodeNameString');
    }

}
 
?>